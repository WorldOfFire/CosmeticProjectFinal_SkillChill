<?php
    session_start();
    require_once __DIR__ . '/vendor/autoload.php';
    require_once 'config.php';

    use Mpdf\Mpdf;

    $user_id = $_SESSION['id_user'];
    $author_name = ($_SESSION['user_index'] ?? '') . ' | ' . ($_SESSION['user_name'] ?? '') . ' ' . ($_SESSION['user_surname'] ?? '');

    $author = ucwords($author_name ?: 'nieznany twórca audytu');
    $date = date('Y-m-d H:i');

    function setHeader(Mpdf $mpdf, $author, $date) {
        $headerHtml = "<div style='font-size:10pt; text-align:right;'>Utworzono przez: {$author} | Data: {$date}</div>";
        $mpdf->SetHTMLHeader($headerHtml);
    }

    function generateUserActivityHtml($user, $activities) {
        $html = "<h2 style='text-align:center;'>Aktywność użytkownika: {$user['name']} {$user['surname']}</h2>";
        $html .= "<p style='text-align:left; margin-left:20px;'>
                    Email: " . htmlspecialchars($user['mail']) . "<br>
                    Login: " . htmlspecialchars($user['login']) . "<br>
                    Index: " . htmlspecialchars($user['employees_index']) . "
                </p>";

        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:75%; margin:0 auto; text-align:center; border-collapse:collapse;">';
        $html .= '<tr style="font-weight:bold; background-color:#f0f0f0;"><th>Data</th><th>Godzina</th></tr>';

        if(!empty($activities)){
            foreach($activities as $a) {
                $html .= '<tr>';
                $html .= '<td>'.date('Y-m-d', strtotime($a['activity_date'])).'</td>';
                $html .= '<td>'.date('H:i', strtotime($a['activity_date'])).'</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td colspan="2">Brak aktywności w ciągu ostatnich 3 miesięcy</td></tr>';
        }

        $html .= '</table><br>';
        return $html;
    }
    if(isset($_POST['audit'])) {
        $target_id = (int)$_POST['audit'];

        $stmt = $pdo->prepare("
            SELECT ce.*, pu.*
            FROM company_employees ce
            JOIN portal_user pu ON ce.id = pu.employees_id
            WHERE pu.id_user = ?
        ");
        $stmt->execute([$target_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("
            SELECT *
            FROM user_activity
            WHERE p_user_id = ?
            AND activity_date >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
            ORDER BY activity_date ASC
        ");
        $stmt->execute([$target_id]);
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mpdf = new Mpdf();
        setHeader($mpdf, $author, $date);
        $html = generateUserActivityHtml($user, $activities);

        $mpdf->WriteHTML($html);
        $mpdf->Output("audit_{$user['name']}_{$user['surname']}.pdf", 'D');
        exit;

    } elseif(isset($_POST['audit-all']) && !empty($_POST['selected_ids'])) {
        $selected_ids = array_map('intval', $_POST['selected_ids']);
        $mpdf = new Mpdf();
        setHeader($mpdf, $author, $date);

        $lastKey = end($selected_ids);
        foreach($selected_ids as $target_id) {
            $stmt = $pdo->prepare("
                SELECT ce.*, pu.*
                FROM company_employees ce
                JOIN portal_user pu ON ce.id = pu.employees_id
                WHERE pu.id_user = ?
            ");
            $stmt->execute([$target_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("
                SELECT *
                FROM user_activity
                WHERE p_user_id = ?
                AND activity_date >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
                ORDER BY activity_date ASC
            ");
            $stmt->execute([$target_id]);
            $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $html = generateUserActivityHtml($user, $activities);
            $mpdf->WriteHTML($html);

            if($target_id !== $lastKey){
                $mpdf->AddPage();
            }
        }

        $mpdf->Output("audit_selected_users.pdf", 'D');
        exit;

    } elseif(isset($_POST['no-activity'])) {
        $stmt = $pdo->prepare("
            SELECT ce.*, pu.*
            FROM company_employees ce
            JOIN portal_user pu ON ce.id = pu.employees_id
            LEFT JOIN user_activity uac ON pu.id_user = uac.p_user_id
            WHERE pu.id_user != ?
            GROUP BY pu.id_user
            HAVING MAX(uac.activity_date) IS NULL
            ORDER BY ce.name ASC, ce.surname ASC
        ");
        $stmt->execute([$user_id]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mpdf = new Mpdf();
        setHeader($mpdf, $author, $date);

        $html = "<h2 style='text-align:center;'>Użytkownicy bez aktywności na portalu</h2>";
        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:75%; margin:0 auto; text-align:center; border-collapse:collapse;">';
        $html .= '<tr style="font-weight:bold; background-color:#f0f0f0;">
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Login</th>
                    <th>Email</th>
                    <th>Index</th>
                </tr>';

        foreach($users as $u) {
            $html .= '<tr>
                <td>' . htmlspecialchars($u['name']) . '</td>
                <td>' . htmlspecialchars($u['surname']) . '</td>
                <td>' . htmlspecialchars($u['login'] ?? '') . '</td>
                <td>' . htmlspecialchars($u['mail']) . '</td>
                <td>' . htmlspecialchars($u['employees_index'] ?? '') . '</td>
            </tr>';
        }

        $html .= '</table>';

        $mpdf->WriteHTML($html);
        $mpdf->Output('inactivity_audit.pdf', 'D');
        exit;
    }
?>
