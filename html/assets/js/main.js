import { initPageLoader } from "./page-loader.js";
import { detectUserAgent } from "./user-agent.js";

document.addEventListener("DOMContentLoaded", () => {
    // 디바이스 환경 감지
    detectUserAgent();

    // 페이지 로딩 애니메이션
    initPageLoader();
});