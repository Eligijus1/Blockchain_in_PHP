<?php
$db = 'data/balance.json';
if (file_exists($db)) {
    $balances = json_decode(file_get_contents($db), true);
} else {
    $balances = ['Eligijus' => 1000000];
    file_put_contents($db, json_encode($balances));
}
switch ($_SERVER['PATH_INFO']) {
    case '/balance':
        $user = $_GET['user'];
        printf("User %s has %d ElygaCoin.", $user, $balances[$user] ?? 0);
        break;
    case '/user':
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $user = $_POST['user'];
            if (isset($balances[$user])) {
                http_response_code(404);//Not Found
                return;
            }
            $balances[$user] = 0;
            file_put_contents($db, json_encode($balances));
            print 'OK';
        } else {
            print 'ERROR: Wrong request method.';
        }
        break;
    case '/transfer':
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $from = $_POST['from'];
            $to = $_POST['to'];
            if (!isset($balances[$from])) {
                http_response_code(404);//Not Found
                return;
            }
            if (!isset($balances[$to])) {
                http_response_code(404);//Not Found
                return;
            }
            $amount = (int)$_POST['amount'];
            if ($amount > $balances[$from]) {
                http_response_code(404);//Not Found
                return;
            }
            $balances[$from] -= $amount;
            $balances[$to] += $amount;
            file_put_contents($db, json_encode($balances));
            print 'OK';
        } else {
            print 'ERROR: Wrong request method.';
        }
        break;
    default:
        print 'ERROR: Unhandled request.';
}
