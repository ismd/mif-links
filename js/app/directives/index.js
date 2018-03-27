'use strict';

var app = require('angular').module('app');

app.directive('listTable', require('./list-table'));
app.directive('periodChooser', require('./period-chooser'));
app.directive('visitsChart', require('./visits-chart'));
