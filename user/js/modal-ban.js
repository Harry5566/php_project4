// modal停權會員視窗

const BanModalModule = (() => {
  const modal = new bootstrap.Modal(document.getElementById('banModal'));

  function bindButtons() {
    document.querySelectorAll('.btn-ban').forEach(btn => {
      btn.addEventListener('click', () => {
        const { memberId, memberName, memberAccount, memberAvatar } = btn.dataset;

        document.getElementById('banMemberId').value = memberId;
        document.getElementById('memberName').textContent = memberName;
        document.getElementById('memberAccount').textContent = '@' + memberAccount;
        document.getElementById('memberAvatar').src = memberAvatar;

        modal.show();
      });
    });
  }

  function bindForm() {
    document.getElementById('banForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const memberId = document.getElementById('banMemberId').value;
      const reason = document.getElementById('banReason').value;

      if (reason) {
        window.location.href = `./doBan.php?id=${memberId}&reason=${encodeURIComponent(reason)}`;
      }
    });
  }

  return {
    init() {
      bindButtons();
      bindForm();
    }
  };
})();