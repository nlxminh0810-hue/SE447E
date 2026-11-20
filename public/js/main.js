// ENGLISHAI Main JavaScript

document.addEventListener('DOMContentLoaded', function () {
    const userSearch = document.querySelector('[data-user-search]');
    const roleFilter = document.querySelector('[data-user-role-filter]');
    const statusFilter = document.querySelector('[data-user-status-filter]');
    const rows = Array.from(document.querySelectorAll('[data-user-row]'));

    function filterUsers() {
        const query = (userSearch?.value || '').trim().toLocaleLowerCase();
        const role = roleFilter?.value || '';
        const status = statusFilter?.value || '';
        rows.forEach(function (row) {
            const matchesQuery = !query || (row.dataset.name + ' ' + row.dataset.email).includes(query);
            const matchesRole = !role || row.dataset.role === role;
            const matchesStatus = !status || row.dataset.status === status;
            row.hidden = !(matchesQuery && matchesRole && matchesStatus);
        });
    }

    [userSearch, roleFilter, statusFilter].forEach(function (control) {
        control?.addEventListener('input', filterUsers);
        control?.addEventListener('change', filterUsers);
    });
});
