# kcalzoa 프로젝트

칼로리 서비스입니다.

## 📂 디렉토리 구조
```sql
CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '제조사 고유 ID',
    name VARCHAR(100) NOT NULL UNIQUE COMMENT '제조사명 (예: 농심, 오리온, 롯데 등)',
    color VARCHAR(20) DEFAULT NULL COMMENT '브랜드 고유 색상 (예: #E2231A)',
    image_url VARCHAR(255) DEFAULT NULL COMMENT '브랜드 로고 또는 이미지 URL',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '등록일시',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE types (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '유형 고유 ID',
    name VARCHAR(50) NOT NULL UNIQUE COMMENT '유형명 (예: 과자, 스낵, 음료 등)',
    description VARCHAR(255) COMMENT '유형 설명',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '등록일시',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시'
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '제품 고유 ID',
    name VARCHAR(100) NOT NULL COMMENT '제품명',
    company_id INT NOT NULL COMMENT '제조사 ID',
    type_id INT NOT NULL COMMENT '제품 유형 ID',
    image_url VARCHAR(255) COMMENT '제품 이미지 URL',
    total_weight VARCHAR(10) COMMENT '총 내용량',
    serving_package INT COMMENT '1봉지당 제공량 수',
    serving_size VARCHAR(10) COMMENT '1회 제공량',
    haccp BOOLEAN DEFAULT FALSE COMMENT 'HACCP 인증 여부 (1: 인증, 0: 미인증)',
    ingredients TEXT COMMENT '원재료명',
    comment_count INT DEFAULT 0 COMMENT '댓글 개수',
    view_count INT DEFAULT 0 COMMENT '조회수';
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '데이터 생성일시',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '데이터 수정일시',
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (type_id) REFERENCES types(id)
) 

CREATE TABLE nutrition (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '영양정보 고유 ID',
    product_id INT NOT NULL COMMENT '제품 ID (products 테이블 참조)',
    calories VARCHAR(20) COMMENT '열량 (예: 470kcal)',
    sodium VARCHAR(20) COMMENT '나트륨 (예: 440mg)',
    carbohydrate VARCHAR(20) COMMENT '탄수화물 (예: 46g)',
    sugar VARCHAR(20) COMMENT '당류 (예: 5g)',
    fat VARCHAR(20) COMMENT '지방 (예: 24g)',
    trans_fat VARCHAR(20) COMMENT '트랜스지방 (예: 0g)',
    saturated_fat VARCHAR(20) COMMENT '포화지방 (예: 8g)',
    cholesterol VARCHAR(20) COMMENT '콜레스테롤 (예: 0mg)',
    protein VARCHAR(20) COMMENT '단백질 (예: 5g)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '등록일시',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
    FOREIGN KEY (product_id) REFERENCES products(id)
);

```
```sql
INSERT INTO companies (name, color, image_url)
VALUES
('농심', '#E60012', '/assets/imgs/brands/nongshim_logo.png'),
('오리온', '#C8322D', '/assets/imgs/brands/orion_logo.svg'),
('롯데제과', '#E51937', '/assets/imgs/brands/lotte_logo.svg'),
('해태제과', '#FF7A00', '/assets/imgs/brands/haitai_logo.svg'),
('크라운제과', '#B72A34', '/assets/imgs/brands/crown_logo.svg'),
('빙그레', '#ED174F', '/assets/imgs/brands/binggrae_logo.svg'),
('삼양식품', '#FF8C1A', '/assets/imgs/brands/samyang_logo.svg');

INSERT INTO types (name, description)
VALUES
('과자', '유탕처리 또는 비유탕 스낵류'),
('라면', '즉석조리식 면류 (컵라면, 봉지라면 등)'),
('음료', '탄산, 커피, 주스 등 마실 수 있는 액상 제품'),
('아이스크림', '빙과류, 아이스바, 콘, 컵 아이스크림 등 냉동 디저트'),
('초콜릿', '코코아버터를 주원료로 한 제품'),
('캔디', '사탕, 젤리, 캐러멜류 등 당과류'),
('빵', '포장된 제빵류, 케이크, 호빵 등'),
('즉석식품', '전자레인지나 물에 데워 먹는 즉석조리 식품');
```