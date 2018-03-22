'use strict';

var app = require('angular').module('app');

app.service('Group', require('./GroupService'));
app.service('Link', require('./LinkService'));
app.service('Stat', require('./StatService'));
