<?php

require_once(dirname(__FILE__) . '/utils.php');

define('BUNDLE_PATH', '/etc/bluestate/bundles');
define('DEFAULT_DB_BUNDLE', 'masterdb');

$DB_CONFIGS = array();

$DB_CONFIGS['CC_DB_CREDS'] = array(
        'db_host' => 'build1',
        'db_port' => '3306',
        'db_name' => 'clientconfig',
        'db_user' => 'clientconfig',
        'db_pass' => 'usnF@1j_qp[1cV64v@Xj',
        'db_ssl' => true,
        'db_ssl_ca' => '/etc/pki/tls/certs/amazon-rds-2015.pem',
);
$DB_CONFIGS['DM_DB_CREDS'] = array(
        'db_host' => 'data-mart.bsdinternal.com',
        'db_port' => '3306',
        'db_name' => 'data_mart',
        'db_user' => 'data_mart',
        'db_pass' => '5awR2$TU',
        'db_ssl' => true,
        'db_ssl_ca' => '/etc/pki/tls/certs/amazon-rds-2015.pem',
);


$DB_CONFIGS['DM_DB_CREDS'] = array(
        'db_host' => '127.0.0.1',
        'db_port' => '3306',
        'db_name' => 'data_mart',
        'db_user' => 'root',
        'db_pass' => 'jms0882',
);


function get_db_credentials($client, $db = DEFAULT_DB_BUNDLE) {
        $bundle_file = BUNDLE_PATH . '/' . $client . '/' . $db . '.json';

        if (!file_exists($bundle_file)) {
                throw new Exception('No config bundle found for client ' . $client . ' / db ' . $db);
        }

        $json = file_get_contents($bundle_file);
        $conf = json_decode($json, true);

        if (!$conf) {
                throw new Exception('JSON error in bundle, client ' . $client . ' / db ' . $db);
        }

        return $conf;
}

function get_db_connection($creds) {
        $conn_options = array();
        if (isset($creds['db_ssl']) && $creds['db_ssl']) {
                $conn_options[PDO::MYSQL_ATTR_SSL_CA] = $creds['db_ssl_ca'];
        }

        try {
            $dbh = new PDO(
                'mysql:host=' . $creds['db_host'] . ';port=' . $creds['db_port'] . ';dbname=' . $creds['db_name'],
                $creds['db_user'],
                $creds['db_pass'],
                $conn_options
            );

            if (!$dbh) {
                throw new Exception('Could not connect');
            }

            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $dbh;
        } catch (Exception $e) {
             throw new Exception("Failed connecting to DB $db_name: " . filter_password($e, $creds['db_pass']));
        }
}

function get_client_db_connection($client) {
        $creds = get_db_credentials($client);
        return get_db_connection($creds);
}

function get_cc_db_connection() {
        global $DB_CONFIGS;
        return get_db_connection($DB_CONFIGS['CC_DB_CREDS']);
}

function get_dm_db_connection() {
        global $DB_CONFIGS;
        return get_db_connection($DB_CONFIGS['DM_DB_CREDS']);
}
