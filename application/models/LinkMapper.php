<?php

class LinkMapper extends PsDbMapper {

    const IGNORED_WORDS = ['admin', 'stat'];

    public function add($link, $groupId) {
        if (!$groupId) {
            throw new Exception('Не задана группа');
        }

        $shortLink = $this->generateShortUrl();

        $stmt = self::$db->prepare("INSERT INTO Links (link, short_link, group_id) VALUES (?, ?, ?)");
        $stmt->bind_param('ssi', $link, $shortLink, $groupId);
        $stmt->execute();

        return [
            'id' => $stmt->insert_id,
            'shortLink' => $shortLink,
            'groupId' => $groupId,
        ];
    }

    public function edit($id, $shortLink, $groupId) {
        if (!$groupId) {
            throw new Exception('Не задана группа');
        }

        $stmt = self::$db->prepare("UPDATE Links SET short_link = ?, group_id = ? WHERE id = ? LIMIT 1");
        $stmt->bind_param('sii', $shortLink, $groupId, $id);
        $stmt->execute();

        return [
            'id' => $id,
            'shortLink' => $shortLink,
            'groupId' => $groupId,
        ];
    }

    public function fetch($where = [], $limit = null, DateTime $from = null, DateTime $to = null) {
        array_walk($where, function(&$value, $key) {
            $value = $key . ' = "' . $value . '"';
        });

        if ($from != null && $to != null) {
            $where[] = "s.visited >= ?";
            $where[] = "s.visited < ?";
        }

        $stmt = self::$db->prepare("SELECT l.id, l.link, l.short_link, l.created, l.group_id, g.title AS group_title, COUNT(s.id) AS stat_count "
            . "FROM Links l "
            . "LEFT JOIN Groups g ON l.group_id = g.id "
            . "LEFT JOIN Stat s ON l.id = s.link_id "
            . (!empty($where) ? "WHERE " . implode(' AND ', $where) . ' ' : '')
            . "GROUP BY l.id "
            . "ORDER BY l.id DESC "
            . (!is_null($limit) ? "LIMIT " . $limit : ''));

        if ($from != null && $to != null) {
            $stmt->bind_param('ss', $from->format('Y-m-d'), $to->format('Y-m-d'));
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $links = [];
        while ($row = $result->fetch_assoc()) {
            $links[] = [
                'id' => $row['id'],
                'link' => $row['link'],
                'short_link' => $row['short_link'],
                'created' => $row['created'],
                'group_id' => $row['group_id'],
                'group_title' => $row['group_title'],
                'stat_count' => $row['stat_count'],
            ];
        }

        return $links;
    }

    public function fetchCount($where = [], DateTime $from = null, DateTime $to = null) {
        array_walk($where, function(&$value, $key) {
            $value = $key . ' = "' . $value . '"';
        });

        if ($from != null && $to != null) {
            $where[] = "s.visited >= ?";
            $where[] = "s.visited < ?";
        }

        $stmt = self::$db->prepare("SELECT 1 "
            . "FROM Links l "
            . "LEFT JOIN Stat s ON l.id = s.link_id "
            . (!empty($where) ? "WHERE " . implode(' AND ', $where) . ' ' : '')
            . "GROUP BY l.id");

        if ($from != null && $to != null) {
            $stmt->bind_param('ss', $from->format('Y-m-d'), $to->format('Y-m-d'));
        }

        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows;
    }

    public function regenerate($id) {
        $shortLink = $this->generateShortUrl();

        $stmt = self::$db->prepare("UPDATE Links SET short_link = ? WHERE id = ? LIMIT 1");
        $stmt->bind_param('si', $shortLink, $id);
        $stmt->execute();

        return $shortLink;
    }

    public function search($link, $groupId = null) {
        $stmt = self::$db->prepare("SELECT l.id, l.link, l.short_link, l.created, l.group_id, g.title AS group_title, COUNT(s.id) AS stat_count " .
                                   "FROM Links l " .
                                   "LEFT JOIN Groups g ON l.group_id = g.id " .
                                   "LEFT JOIN Stat s ON l.id = s.link_id " .
                                   "WHERE (l.link LIKE '%" . $link . "%' OR l.short_link LIKE '%" . $link . "%') " .
                                   ($groupId ? "AND g.id = ? " : "") .
                                   "GROUP BY l.id " .
                                   "ORDER BY l.id DESC");

        if ($groupId) {
            $stmt->bind_param('i', $groupId);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $links = [];
        while ($row = $result->fetch_assoc()) {
            $links[] = [
                'id' => $row['id'],
                'link' => $row['link'],
                'short_link' => $row['short_link'],
                'created' => $row['created'],
                'group_id' => $row['group_id'],
                'group_title' => $row['group_title'],
                'stat_count' => $row['stat_count'],
            ];
        }

        return $links;
    }

    protected function generateShortUrl() {
        $i = 0;

        do {
            $shortLink = $this->alphaID($this->generateNumber(), false, false, PsConfig::getInstance()->main->pass_key);
        } while ((in_array($shortLink, self::IGNORED_WORDS) || count($this->fetch([
            'short_link' => $shortLink,
        ])) > 0) && ++$i < 3);

        if ($i == 3) {
            throw new Exception('Не удалось создать ссылку');
        }

        return $shortLink;
    }

    /**
     * @return LinkMapper
     */
    public static function getInstance($options = []) {
        return parent::getInstance($options);
    }

    /**
     * Generates id
     * @return int
     */
    protected function generateNumber() {
        $microtime = microtime(true);
        $microtime = (int)str_replace('.', '', $microtime);
        $microtime /= rand(10000, 100000);
        $microtime = (int)$microtime;

        return $microtime;
    }

    /**
     * Translates a number to a short alhanumeric version
     *
     * Translated any number up to 9007199254740992
     * to a shorter version in letters e.g.:
     * 9007199254740989 --> PpQXn7COf
     *
     * specifiying the second argument true, it will
     * translate back e.g.:
     * PpQXn7COf --> 9007199254740989
     *
     * this function is based on any2dec && dec2any by
     * fragmer[at]mail[dot]ru
     * see: http://nl3.php.net/manual/en/function.base-convert.php#52450
     *
     * If you want the alphaID to be at least 3 letter long, use the
     * $pad_up = 3 argument
     *
     * In most cases this is better than totally random ID generators
     * because this can easily avoid duplicate ID's.
     * For example if you correlate the alpha ID to an auto incrementing ID
     * in your database, you're done.
     *
     * The reverse is done because it makes it slightly more cryptic,
     * but it also makes it easier to spread lots of IDs in different
     * directories on your filesystem. Example:
     * $part1 = substr($alpha_id,0,1);
     * $part2 = substr($alpha_id,1,1);
     * $part3 = substr($alpha_id,2,strlen($alpha_id));
     * $destindir = "/".$part1."/".$part2."/".$part3;
     * // by reversing, directories are more evenly spread out. The
     * // first 26 directories already occupy 26 main levels
     *
     * more info on limitation:
     * - http://blade.nagaokaut.ac.jp/cgi-bin/scat.rb/ruby/ruby-talk/165372
     *
     * if you really need this for bigger numbers you probably have to look
     * at things like: http://theserverpages.com/php/manual/en/ref.bc.php
     * or: http://theserverpages.com/php/manual/en/ref.gmp.php
     * but I haven't really dugg into this. If you have more info on those
     * matters feel free to leave a comment.
     *
     * The following code block can be utilized by PEAR's Testing_DocTest
     * <code>
     * // Input //
     * $number_in = 2188847690240;
     * $alpha_in  = "SpQXn7Cb";
     *
     * // Execute //
     * $alpha_out  = alphaID($number_in, false, 8);
     * $number_out = alphaID($alpha_in, true, 8);
     *
     * if ($number_in != $number_out) {
     *   echo "Conversion failure, ".$alpha_in." returns ".$number_out." instead of the ";
     *   echo "desired: ".$number_in."\n";
     * }
     * if ($alpha_in != $alpha_out) {
     *   echo "Conversion failure, ".$number_in." returns ".$alpha_out." instead of the ";
     *   echo "desired: ".$alpha_in."\n";
     * }
     *
     * // Show //
     * echo $number_out." => ".$alpha_out."\n";
     * echo $alpha_in." => ".$number_out."\n";
     * echo alphaID(238328, false)." => ".alphaID(alphaID(238328, false), true)."\n";
     *
     * // expects:
     * // 2188847690240 => SpQXn7Cb
     * // SpQXn7Cb => 2188847690240
     * // aaab => 238328
     *
     * </code>
     *
     * @author  Kevin van Zonneveld &lt;kevin@vanzonneveld.net>
     * @author  Simon Franz
     * @author  Deadfish
     * @author  SK83RJOSH
     * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
     * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
     * @version   SVN: Release: $Id: alphaID.inc.php 344 2009-06-10 17:43:59Z kevin $
     * @link    http://kevin.vanzonneveld.net/
     *
     * @param mixed   $in   String or long input to translate
     * @param boolean $to_num  Reverses translation when true
     * @param mixed   $pad_up  Number or boolean padds the result up to a specified length
     * @param string  $pass_key Supplying a password makes it harder to calculate the original ID
     *
     * @return mixed string or long
     */
    protected function alphaID($in, $to_num = false, $pad_up = false, $pass_key = null) {
        $out   =   '';
        $index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base  = strlen($index);

        if ($pass_key !== null) {
            // Although this function's purpose is to just make the
            // ID short - and not so much secure,
            // with this patch by Simon Franz (http://blog.snaky.org/)
            // you can optionally supply a password to make it harder
            // to calculate the corresponding numeric ID

            for ($n = 0; $n < strlen($index); $n++) {
                $i[] = substr($index, $n, 1);
            }

            $pass_hash = hash('sha256',$pass_key);
            $pass_hash = (strlen($pass_hash) < strlen($index) ? hash('sha512', $pass_key) : $pass_hash);

            for ($n = 0; $n < strlen($index); $n++) {
                $p[] =  substr($pass_hash, $n, 1);
            }

            array_multisort($p, SORT_DESC, $i);
            $index = implode($i);
        }

        if ($to_num) {
            // Digital number  <<--  alphabet letter code
            $len = strlen($in) - 1;

            for ($t = $len; $t >= 0; $t--) {
                $bcp = bcpow($base, $len - $t);
                $out = $out + strpos($index, substr($in, $t, 1)) * $bcp;
            }

            if (is_numeric($pad_up)) {
                $pad_up--;

                if ($pad_up > 0) {
                    $out -= pow($base, $pad_up);
                }
            }
        } else {
            // Digital number  -->>  alphabet letter code
            if (is_numeric($pad_up)) {
                $pad_up--;

                if ($pad_up > 0) {
                    $in += pow($base, $pad_up);
                }
            }

            for ($t = ($in != 0 ? floor(log($in, $base)) : 0); $t >= 0; $t--) {
                $bcp = bcpow($base, $t);
                $a   = floor($in / $bcp) % $base;
                $out = $out . substr($index, $a, 1);
                $in  = $in - ($a * $bcp);
            }
        }

        return $out;
    }
}
