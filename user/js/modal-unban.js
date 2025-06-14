
const UnbanModalModule = (() => {
  const modal = new bootstrap.Modal(document.getElementById('unbanModal'));

  function bindButtons() {
    document.querySelectorAll('.btn-unban').forEach(btn => {
      btn.addEventListener('click', () => {
        const { memberId, memberName, memberAccount, memberAvatar } = btn.dataset;

        document.getElementById('unbanMemberId').value = memberId;
        document.getElementById('unbanMemberName').textContent = memberName;
        document.getElementById('unbanMemberAccount').textContent = '@' + memberAccount;
        document.getElementById('unbanMemberAvatar').src = memberAvatar;

        modal.show();
      });
    });
  }

  function bindForm() {
    document.getElementById('unbanForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const memberId = document.getElementById('unbanMemberId').value;

      if (memberId) {
        window.location.href = `./doUnban.php?id=${memberId}`;
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