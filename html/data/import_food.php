<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: text/plain; charset=utf-8');

// =============================
// 1️⃣ JSON 파일 읽기
// =============================
$json_file = __DIR__ . '/data_food.json';
if (!file_exists($json_file)) {
    die("❌ JSON 파일이 없습니다: {$json_file}\n");
}

$jsonData = file_get_contents($json_file);
$data = json_decode($jsonData, true);
if (!$data) {
    die("❌ JSON 파싱 오류\n");
}

echo "✅ JSON 데이터 로드 완료\n\n";

// =============================
// 2️⃣ 데이터 삽입 (UPSERT)
// =============================
foreach ($data as $item) {
    $name        = trim($item['name']);
    $companyName = trim($item['company']);
    $typeName    = trim($item['type']);
    $image       = trim($item['images']);
    $pkg         = $item['package'];
    $nutrition   = $item['nutrition'];
    $ingredients = implode(", ", $item['ingredients']);
    $haccp       = $item['haccp'] ? 1 : 0;

    // -----------------------------
    // 제조사 ID 조회
    // -----------------------------
    $stmt = $mysqli->prepare("SELECT id FROM companies WHERE name = ?");
    $stmt->bind_param("s", $companyName);
    $stmt->execute();
    $company = $stmt->get_result()->fetch_assoc();
    $company_id = $company['id'] ?? null;
    if (!$company_id) {
        echo "⚠️ 제조사 없음: {$companyName}\n";
        continue;
    }

    // -----------------------------
    // 유형 ID 조회
    // -----------------------------
    $stmt = $mysqli->prepare("SELECT id FROM types WHERE name = ?");
    $stmt->bind_param("s", $typeName);
    $stmt->execute();
    $type = $stmt->get_result()->fetch_assoc();
    $type_id = $type['id'] ?? null;
    if (!$type_id) {
        echo "⚠️ 유형 없음: {$typeName}\n";
        continue;
    }

    // -----------------------------
    // 제품(products) UPSERT
    // -----------------------------
    $stmt = $mysqli->prepare("
        INSERT INTO products 
        (name, company_id, type_id, image_url, total_weight, serving_package, serving_size, haccp, ingredients)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            company_id = VALUES(company_id),
            type_id = VALUES(type_id),
            image_url = VALUES(image_url),
            total_weight = VALUES(total_weight),
            serving_package = VALUES(serving_package),
            serving_size = VALUES(serving_size),
            haccp = VALUES(haccp),
            ingredients = VALUES(ingredients),
            updated_at = CURRENT_TIMESTAMP
    ");
    $stmt->bind_param(
        "siissssss",
        $name,
        $company_id,
        $type_id,
        $image,
        $pkg['total_weight'],
        $pkg['serving_package'],
        $pkg['serving_size'],
        $haccp,
        $ingredients
    );
    $stmt->execute();

    // 등록된 product_id 가져오기
    $product_id = $mysqli->insert_id;
    if ($product_id == 0) {
        // 이미 존재하면 id 다시 조회
        $stmt = $mysqli->prepare("SELECT id FROM products WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $product_id = $stmt->get_result()->fetch_assoc()['id'] ?? null;
    }

    if (!$product_id) {
        echo "❌ 제품 등록 실패: {$name}\n";
        continue;
    }

    echo "✅ 제품 등록/갱신 완료: {$name} (ID: {$product_id})\n";

    // -----------------------------
    // 영양정보(nutrition) UPSERT
    // -----------------------------
    $calories       = $nutrition['열량'] ?? null;
    $sodium         = $nutrition['나트륨'] ?? null;
    $carbohydrate   = $nutrition['탄수화물'] ?? null;
    $sugar          = $nutrition['당류'] ?? null;
    $fat            = $nutrition['지방'] ?? null;
    $trans_fat      = $nutrition['트랜스지방'] ?? null;
    $saturated_fat  = $nutrition['포화지방'] ?? null;
    $cholesterol    = $nutrition['콜레스테롤'] ?? null;
    $protein        = $nutrition['단백질'] ?? null;

    $stmt = $mysqli->prepare("
        INSERT INTO nutrition 
        (product_id, calories, sodium, carbohydrate, sugar, fat, trans_fat, saturated_fat, cholesterol, protein)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            calories = VALUES(calories),
            sodium = VALUES(sodium),
            carbohydrate = VALUES(carbohydrate),
            sugar = VALUES(sugar),
            fat = VALUES(fat),
            trans_fat = VALUES(trans_fat),
            saturated_fat = VALUES(saturated_fat),
            cholesterol = VALUES(cholesterol),
            protein = VALUES(protein),
            updated_at = CURRENT_TIMESTAMP
    ");
    $stmt->bind_param(
        "isssssssss",
        $product_id,
        $calories,
        $sodium,
        $carbohydrate,
        $sugar,
        $fat,
        $trans_fat,
        $saturated_fat,
        $cholesterol,
        $protein
    );
    $stmt->execute();

    echo "   ↳ 영양정보 등록/갱신 완료 ✅\n\n";
}

echo "\n🎉 모든 제품 및 영양정보 처리 완료!\n";
?>
