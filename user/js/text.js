// // Modal instances
// const banModal = new bootstrap.Modal(document.getElementById('banModal'));
// const unbanModal = new bootstrap.Modal(document.getElementById('unbanModal'));
// const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

// // // Ban member functionality
// document.querySelectorAll('.btn-ban').forEach(btn => {
//   btn.addEventListener('click', function () {
//     const memberId = this.dataset.memberId;
//     const memberName = this.dataset.memberName;
//     const memberAccount = this.dataset.memberAccount;
//     const memberAvatar = this.dataset.memberAvatar;

//     document.getElementById('banMemberId').value = memberId;
//     document.getElementById('memberName').textContent = memberName;
//     document.getElementById('memberAccount').textContent = '@' + memberAccount;
//     document.getElementById('memberAvatar').src = memberAvatar;

//     banModal.show();
//   });
// });

// // Unban member functionality
// document.querySelectorAll('.btn-unban').forEach(btn => {
//   btn.addEventListener('click', function () {
//     const memberId = this.dataset.memberId;
//     const memberName = this.dataset.memberName;
//     const memberAccount = this.dataset.memberAccount;
//     const memberAvatar = this.dataset.memberAvatar;

//     document.getElementById('unbanMemberId').value = memberId;
//     document.getElementById('unbanMemberName').textContent = memberName;
//     document.getElementById('unbanMemberAccount').textContent = '@' + memberAccount;
//     document.getElementById('unbanMemberAvatar').src = memberAvatar;

//     unbanModal.show();
//   });
// });

// // Delete member functionality
// document.querySelectorAll('.btn-delete').forEach(btn => {
//   btn.addEventListener('click', function () {
//     const memberId = this.dataset.memberId;
//     const memberName = this.dataset.memberName;

//     document.getElementById('deleteMemberId').value = memberId;
//     document.getElementById('deleteMemberName').textContent = memberName;

//     deleteModal.show();
//   });
// });

// // Ban form submission
// document.getElementById('banForm').addEventListener('submit', function (e) {
//   e.preventDefault();
//   const memberId = document.getElementById('banMemberId').value;
//   const reason = document.getElementById('banReason').value;

//   if (reason) {
//     window.location.href = `./doBan.php?id=${memberId}&reason=${encodeURIComponent(reason)}`;
//   }
// });

// // Unban form submission
// document.getElementById('unbanForm').addEventListener('submit', function (e) {
//   e.preventDefault();
//   const memberId = document.getElementById('unbanMemberId').value;

//   if (memberId) {
//     window.location.href = `./doUnban.php?id=${memberId}`;
//   }
// });

// // Delete confirmation
// document.getElementById('confirmDelete').addEventListener('click', function () {
//   const memberId = document.getElementById('deleteMemberId').value;
//   window.location.href = `./doDelete.php?id=${memberId}`;
// });


// Quick search functionality
// const quickSearch = document.getElementById('quickSearch');
// if (quickSearch) {
//   quickSearch.addEventListener('keypress', function (e) {
//     if (e.key === 'Enter') {
//       e.preventDefault();
//       const searchValue = this.value.trim();
//       const currentUrl = new URL(window.location.href);
//       if (searchValue) {
//         currentUrl.searchParams.set('search', searchValue);
//       } else {
//         currentUrl.searchParams.delete('search');
//       }
//       currentUrl.searchParams.delete('page'); // Reset to first page
//       window.location.href = currentUrl.toString();
//     }
//   });
// }