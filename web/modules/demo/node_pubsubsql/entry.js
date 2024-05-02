import { PubSubSqlClient } from './lib/client';

var createClient = function () {
  return new PubSubSqlClient();
}
