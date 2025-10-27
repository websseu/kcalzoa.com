<?php
    require_once __DIR__ . '/../config.php';

    // 메타데이터
    $page_title = "KcalZoa | 음식 칼로리 · 영양성분 정보 검색 서비스";
    $page_desc  = "칼로리, 탄수화물, 단백질, 지방, 나트륨 등 음식 영양정보를 KcalZoa에서 한 번에 확인하세요. 다이어트, 식단관리, 건강관리 필수 정보 제공.";
    $page_keyword = "칼로리, 음식 칼로리, 음식 영양정보, 탄단지, 다이어트, 식단관리, 음식 검색, 건강식단, kcalzoa";
    $body_class = "site-main";

    require_once __DIR__ . '/../includes/site-head.php';
    require_once __DIR__ . '/../includes/site-header.php';
    require_once __DIR__ . '/../includes/search-area.php';
?>


<?php 
    require_once __DIR__ . '/../includes/site-footer.php'; 
?>