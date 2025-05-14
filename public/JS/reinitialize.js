function reinitializePage(){
    initSidebar();        // từ sidebar.js
    initProfileForm();    // từ profile.js
    initEmployeesTable(); // từ employees.js
    // … các init khác nếu có
}
// gọi 1 lần để khởi tạo page đầu
reinitializePage();