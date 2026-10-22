// Xác nhận trước khi gửi các form có thuộc tính data-confirm (ví dụ: xóa bản ghi).
// Dùng file JS riêng thay cho onclick inline để tương thích Content-Security-Policy.
document.addEventListener('submit', function (event) {
    var message = event.target.getAttribute('data-confirm');
    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});
