<?php
    session_start();
    require_once 'config.php';
    require_once 'country_map.php';

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error','msg'=>'Nieprawidłowe żądanie']); 
        exit;
    }

    $user_id = (int)($_POST['user_id'] ?? 0);
    if (!$user_id) { 
        echo json_encode(['status'=>'error','msg'=>'Nieprawidłowy użytkownik']); 
        exit; 
    }

    $stmt = $pdo->prepare("SELECT * FROM company_employees ce LEFT JOIN portal_user pu ON ce.id = pu.employees_id WHERE ce.id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) { 
        echo json_encode(['status'=>'error','msg'=>'Użytkownik nie istnieje']); 
        exit; 
    }

    if (!empty($_POST['mail']) && $_POST['mail'] !== $user['mail']) {
        $stmt = $pdo->prepare("SELECT id FROM company_employees WHERE mail = ? AND id != ?");
        $stmt->execute([$_POST['mail'],$user_id]);
        if ($stmt->fetch()) { 
            echo json_encode(['status'=>'error','field'=>'mail']); 
            exit; 
        }
    }

    $countryCode = $_POST['country'] ?? '';
    if (!array_key_exists($countryCode, $countryMap)) { 
        echo json_encode(['status'=>'error','field'=>'country']); 
        exit; 
    }
    $countryName = $countryMap[$countryCode];

    $newIndex = $_POST['employees_index'] ?? $user['employees_index'] ?? '';

    function generateLogin($name, $surname, $pdo, $user_id){
        $map = [
            'ą'=>"a",'ć'=>"c",'ę'=>"e",'ł'=>"l",'ń'=>"n",'ó'=>"o",'ś'=>"s",'ź'=>"z",'ż'=>"z",
            'Ą'=>"a",'Ć'=>"c",'Ę'=>"e",'Ł'=>"l",'Ń'=>"n",'Ó'=>"o",'Ś'=>"s",'Ź'=>"z",'Ż'=>"z"
        ];
        $first = $name ? mb_strtolower(mb_substr($name,0,1)) : '';
        $surnameLower = $surname ? mb_strtolower($surname) : '';
        $clean = strtr($first.$surnameLower,$map);
        $clean = preg_replace('/\s+/','',$clean);
        $base = $clean; 
        $i = 1;
        while(true){
            $stmt = $pdo->prepare("SELECT id_user FROM portal_user WHERE login=? AND employees_id!=?");
            $stmt->execute([$clean,$user_id]);
            if(!$stmt->fetch()) break;
            $clean = $base.$i; 
            $i++;
        }
        return $clean;
    }

    $updateFields = [];
    $params = [];

    if (!empty($_POST['name'])) {
        $updateFields[] = "name=?";
        $params[] = $_POST['name'];
    }
    if (!empty($_POST['surname'])) {
        $updateFields[] = "surname=?";
        $params[] = $_POST['surname'];
    }

    $updateFields[] = "employees_index=?";
    $params[] = $newIndex;

    $updateFields[] = "country=?";
    $params[] = $countryName;

    if (!empty($_POST['mail'])) {
        $updateFields[] = "mail=?";
        $params[] = $_POST['mail'];
    }

    $params[] = $user_id;
    $stmt = $pdo->prepare("UPDATE company_employees SET ".implode(',', $updateFields)." WHERE id=?");
    $stmt->execute($params);

    $newLogin = generateLogin($_POST['name'] ?? $user['name'], $_POST['surname'] ?? $user['surname'], $pdo, $user_id);
    if($newLogin !== $user['login']){
        $stmt = $pdo->prepare("UPDATE portal_user SET login=? WHERE employees_id=?");
        $stmt->execute([$newLogin,$user_id]);
        file_put_contents(__DIR__.'/userdata/login_changes.txt',"Stary login: {$user['login']} | Nowy login: $newLogin\n",FILE_APPEND);
    }

    if(isset($_POST['access_level'])){
        $stmt = $pdo->prepare("UPDATE portal_user SET access=? WHERE employees_id=?");
        $stmt->execute([(int)$_POST['access_level'],$user_id]);
    }

    echo json_encode(['status'=>'success','msg'=>'Dane użytkownika zostały zaktualizowane']);
    exit;
?>
