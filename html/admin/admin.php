<?php
    session_start();

    // 로그인 세션 검사
    if (empty($_SESSION['admin_auth'])) {
        header("Location: admin-login.php");
        exit;
    }

    // 세션 만료 
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > ($_SESSION['expire_time'] ?? 18000)) {
        session_unset();
        session_destroy();
        header("Location: admin-login.php?expired=1");
        exit;
    }

    require_once __DIR__ . '/../config.php';

    // ------------------------------
    // 2️⃣ 검색 기능
    // ------------------------------
    $keyword = trim($_GET['q'] ?? '');

    $sql = "
        SELECT 
            p.id,
            p.name AS product_name,
            c.name AS company_name,
            t.name AS type_name,
            p.image_url,
            p.total_weight,
            p.serving_package,
            p.serving_size,
            p.haccp,
            p.comment_count,
            p.view_count,
            p.created_at,
            p.updated_at
        FROM products p
        LEFT JOIN companies c ON p.company_id = c.id
        LEFT JOIN types t ON p.type_id = t.id
    ";

    if ($keyword !== '') {
        $sql .= " WHERE p.name LIKE CONCAT('%', ?, '%') OR c.name LIKE CONCAT('%', ?, '%')";
    }

    $sql .= " ORDER BY p.id DESC";

    $stmt = $mysqli->prepare($sql);
    if ($keyword !== '') {
        $stmt->bind_param("ss", $keyword, $keyword);
    }
    $stmt->execute();
    $result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 페이지 | kcalzoa</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" href="/assets/img/favicon.ico">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="admin-site">
    <header>
        <h1><a href="/">Kcalzoa</a></h1>
    </header>

    <main>
        <section>
            <form class="search" method="get" action="">
                <input type="text" name="q" placeholder="번호 또는 제목으로 검색" value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit">검색</button>
            </form>
        </section>
        

        <div class="table-wrap">
            <table>
                <colgroup>
                    <col style="width: 60px">
                    <col style="width: 80px">
                    <col style="width: 80px">
                    <col style="width: 80px">
                    <col >
                    <col style="width: 70px">
                    <col style="width: 70px">
                    <col style="width: 70px">
                    <col style="width: 70px">
                    <col style="width: 70px">
                    <col style="width: 220px">
                </colgroup>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>사진</th>
                    <th>회사</th>
                    <th>유형</th>
                    <th>제목</th>
                    <th>봉지수</th>
                    <th>내용량</th>
                    <th>토탈</th>
                    <th>댓글</th>
                    <th>뷰</th>
                    <th>날짜</th>
                </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="center"><?= $row['id'] ?></td>
                                <td class="center img">
                                    <?php if (!empty($row['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" >
                                    <?php else: ?>
                                        <img src="/assets/img/kcalzoa.png" alt="이미지 없음" />
                                    <?php endif; ?>
                                </td>
                                <td class="center"><?= htmlspecialchars($row['company_name']) ?></td>
                                <td class="center"><?= htmlspecialchars($row['type_name']) ?></td>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td class="center"><?= htmlspecialchars($row['serving_package']) ?></td>
                                <td class="center"><?= htmlspecialchars($row['serving_size']) ?></td>
                                <td class="center"><?= htmlspecialchars($row['total_weight']) ?></td>
                                <td class="center"><?= htmlspecialchars($row['comment_count']) ?></td>
                                <td class="center"><?= htmlspecialchars($row['view_count']) ?></td>
                                <td class="center"><?= htmlspecialchars($row['created_at']) ?></td>



                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" style="text-align:center; padding:20px;">등록된 제품이 없습니다.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    
</body>
</html>
