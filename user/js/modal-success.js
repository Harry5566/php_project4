const SuccessModalModule = (() => {
  const modalElement = document.getElementById('successModal');
  const messageElement = document.getElementById('successMessage');
  const confirmButton = modalElement.querySelector('.btn-success'); // 綁定按鈕
  const modal = new bootstrap.Modal(modalElement);

  let onConfirmCallback = null;

  function show(message = "操作已完成", onConfirm = null) {
    messageElement.textContent = message;
    onConfirmCallback = onConfirm;
    modal.show();
  }

  // 當使用者點「確認」
  confirmButton.addEventListener('click', () => {
    if (typeof onConfirmCallback === 'function') {
      onConfirmCallback(); // 執行跳轉或其他動作
      onConfirmCallback = null; // 清空避免記憶
    }
  });

  return {
    show
  };
})();