
const DeleteModalModule = (() => {
  const modal = new bootstrap.Modal(document.getElementById('deleteModal'));

  function bindButtons() {
    document.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', () => {
        const { memberId, memberName } = btn.dataset;

        document.getElementById('deleteMemberId').value = memberId;
        document.getElementById('deleteMemberName').textContent = memberName;

        modal.show();
      });
    });
  }

  function bindForm() {
    document.getElementById('confirmDelete').addEventListener('click', function () {
      const memberId = document.getElementById('deleteMemberId').value;
      window.location.href = `./doDelete.php?id=${memberId}`;

    });
  }

  return {
    init() {
      bindButtons();
      bindForm();
    }
  };
})();