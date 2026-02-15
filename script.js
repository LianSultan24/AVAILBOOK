// ========== API Configuration ==========
const API_BASE_URL = 'http://localhost/etok2'; // I-update ni base sa imong local path

// ========== Global Variables ==========
let services = [];
let appointments = [];
let accounts = [];
let filteredAppointments = [];
let autoRefreshInterval = null;
const AUTO_REFRESH_TIME = 30000; // 30 seconds

// ========== Auto Refresh Functions ==========
function startAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    
    autoRefreshInterval = setInterval(async () => {
        console.log('Auto-refreshing data...');
        await updateStats();
        
        const activePage = document.querySelector('.page-content.active');
        if (activePage) {
            const pageId = activePage.id;
            
            switch(pageId) {
                case 'dashboard':
                    await renderDashboardTable();
                    break;
                case 'appointments':
                    await fetchAppointments();
                    await applyFilters();
                    break;
                case 'calendar':
                    if (calendar) {
                        calendar.refetchEvents();
                    }
                    break;
                case 'history':
                    const historyTable = document.getElementById('historyTable');
                    if (historyTable) {
                        await renderHistoryTable();
                    }
                    break;
                case 'archive':
                    await renderArchiveTable();
                    await renderAccountsArchiveTable();
                    break;
            }
        }
    }, AUTO_REFRESH_TIME);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
}

// ========== API Calls ==========
async function fetchStats() {
    try {
        const response = await fetch(`${API_BASE_URL}/api_stats.php`);
        const result = await response.json();
        if (result.success) {
            return result.data;
        }
    } catch (error) {
        console.error('Error fetching stats:', error);
    }
    return null;
}

async function fetchAppointments(filters = {}) {
    try {
        let url = `${API_BASE_URL}/api_appointments.php?`;
        if (filters.status) url += `status=${filters.status}&`;
        if (filters.service_id) url += `service_id=${filters.service_id}&`;
        if (filters.search) url += `search=${filters.search}&`;
        
        const response = await fetch(url);
        const result = await response.json();
        if (result.success) {
            appointments = result.data;
            filteredAppointments = result.data;
            return result.data;
        }
    } catch (error) {
        console.error('Error fetching appointments:', error);
    }
    return [];
}

async function fetchUsers() {
    try {
        const response = await fetch(`${API_BASE_URL}/api_users.php`);
        const result = await response.json();
        if (result.success) {
            accounts = result.data;
            return result.data;
        }
    } catch (error) {
        console.error('Error fetching users:', error);
    }
    return [];
}

async function updateAppointmentStatus(id, status) {
    try {
        const response = await fetch(`${API_BASE_URL}/api_appointments.php`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                Appointment_ID: id,
                Status: status
            })
        });
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error updating appointment:', error);
        return { success: false, message: 'Error updating appointment' };
    }
}

async function deleteAppointment(id) {
    try {
        const response = await fetch(`${API_BASE_URL}/api_appointments.php?id=${id}`, {
            method: 'DELETE'
        });
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error deleting appointment:', error);
        return { success: false, message: 'Error deleting appointment' };
    }
}

async function createUser(userData) {
    try {
        const response = await fetch(`${API_BASE_URL}/api_users.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        });
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error creating user:', error);
        return { success: false, message: 'Error creating user' };
    }
}

async function toggleUserStatus(id, status) {
    try {
        const response = await fetch(`${API_BASE_URL}/api_users.php`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                User_ID: id,
                Status: status
            })
        });
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error toggling user status:', error);
        return { success: false, message: 'Error toggling user status' };
    }
}

async function archiveUser(id) {
    try {
        const response = await fetch(`${API_BASE_URL}/api_users.php?id=${id}`, {
            method: 'DELETE'
        });
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error archiving user:', error);
        return { success: false, message: 'Error archiving user' };
    }
}

async function fetchArchive() {
    try {
        const response = await fetch(`${API_BASE_URL}/api_archive.php`);
        const result = await response.json();
        if (result.success) {
            return result.data;
        }
    } catch (error) {
        console.error('Error fetching archive:', error);
    }
    return [];
}

async function fetchAccountsArchive() {
    try {
        const response = await fetch(`${API_BASE_URL}/api_accounts_archive.php`);
        const result = await response.json();
        if (result.success) {
            return result.data;
        }
    } catch (error) {
        console.error('Error fetching accounts archive:', error);
    }
    return [];
}

async function unarchiveAppointment(archiveId) {
    try {
        const response = await fetch(`${API_BASE_URL}/api_archive.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                Archive_ID: archiveId
            })
        });
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error unarchiving appointment:', error);
        return { success: false, message: 'Error unarchiving appointment' };
    }
}

async function unarchiveAccount(archiveId) {
    try {
        const response = await fetch(`${API_BASE_URL}/api_accounts_archive.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                Archive_ID: archiveId
            })
        });
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error unarchiving account:', error);
        return { success: false, message: 'Error unarchiving account' };
    }
}

async function fetchHistory() {
    try {
        const response = await fetch(`${API_BASE_URL}/api_history.php`);
        const result = await response.json();
        if (result.success) {
            return result.data;
        }
    } catch (error) {
        console.error('Error fetching history:', error);
    }
    return [];
}

async function logHistory(action, details) {
    try {
        const response = await fetch(`${API_BASE_URL}/api_history.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                Action: action,
                Details: details
            })
        });
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error logging history:', error);
        return { success: false, message: 'Error logging history' };
    }
}

// ========== Navigation Functions ==========
document.querySelectorAll('.menu-link').forEach(link => {
    link.addEventListener('click', async function(e) {
        e.preventDefault();
        const page = this.dataset.page;
        
        document.querySelectorAll('.menu-link').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
        
        document.querySelectorAll('.page-content').forEach(p => p.classList.remove('active'));
        document.getElementById(page).classList.add('active');
        
        const titles = {
            'dashboard': 'Dashboard',
            'appointments': 'Appointments',
            'calendar': 'Calendar',
            'history': 'History',
            'accounts': 'Accounts',
            'archive': 'Archive'
        };
        document.getElementById('pageTitle').textContent = titles[page] || 'Dashboard';
        
        if (page === 'appointments') await renderAppointmentsTable();
        if (page === 'history') {
            const historyTable = document.getElementById('historyTable');
            if (historyTable) {
                await renderHistoryTable();
            }
        }
        if (page === 'accounts') await renderAccounts();
        if (page === 'archive') {
            await renderArchiveTable();
            await renderAccountsArchiveTable();
        }
        if (page === 'calendar') {
            if (!calendar) {
                initializeCalendar();
            } else {
                calendar.refetchEvents();
            }
        }
    });
});

// ========== Helper Functions ==========
async function updateStats() {
    const stats = await fetchStats();
    if (stats) {
        document.getElementById('totalAppointments').textContent = stats.total || 0;
        document.getElementById('pendingCount').textContent = stats.pending || 0;
        document.getElementById('approvedCount').textContent = stats.approved || 0;
        document.getElementById('completedCount').textContent = stats.completed || 0;
        document.getElementById('cancelledCount').textContent = stats.cancelled || 0;
    }
}

// ========== Dashboard Functions ==========
async function renderDashboardTable() {
    await fetchAppointments();
    const tbody = document.getElementById('dashboardTable');
    tbody.innerHTML = '';
    
    const recent = appointments.slice(0, 5);
    recent.forEach(apt => {
        const badgeClass = `badge-${apt.Status}`;
        tbody.innerHTML += `
            <tr>
                <td>#${apt.Appointment_ID}</td>
                <td>${apt.Customer_Name}</td>
                <td>${apt.Service_Name || 'N/A'}</td>
                <td>${apt.Appointment_date}</td>
                <td><span class="badge ${badgeClass}">${apt.Status}</span></td>
            </tr>
        `;
    });
}

// ========== Appointments Functions ==========
async function renderAppointmentsTable() {
    const tbody = document.getElementById('appointmentsTable');
    tbody.innerHTML = '';
    
    if (filteredAppointments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No appointments found</td></tr>';
        return;
    }
    
    filteredAppointments.forEach(apt => {
        const badgeClass = `badge-${apt.Status}`;
        
        // Service Mode badge
        let serviceModeBadge = '';
        if (apt.Service_Mode === 'home_service') {
            serviceModeBadge = '<span class="badge bg-primary"><i class="bi bi-house"></i> Home Service</span>';
        } else if (apt.Service_Mode === 'store_service') {
            serviceModeBadge = '<span class="badge bg-info"><i class="bi bi-shop"></i> Store Service</span>';
        } else {
            serviceModeBadge = '<span class="badge bg-secondary">N/A</span>';
        }
        
        tbody.innerHTML += `
            <tr>
                <td>#${apt.Appointment_ID}</td>
                <td>${apt.Customer_Name}</td>
                <td>${apt.Service_Name || 'N/A'}</td>
                <td>${serviceModeBadge}</td>
                <td>${apt.Car_type}</td>
                <td>${apt.Car_Model}</td>
                <td>${apt.Appointment_date}</td>
                <td>${apt.Appointment_time}</td>
                <td><span class="badge ${badgeClass}">${apt.Status}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-info btn-sm btn-action" onclick="viewAppointment(${apt.Appointment_ID})" data-bs-toggle="modal" data-bs-target="#viewModal">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm btn-action" onclick="editAppointment(${apt.Appointment_ID})" data-bs-toggle="modal" data-bs-target="#editModal">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-success btn-sm btn-action" onclick="sendEmailManual(${apt.Appointment_ID})" title="Send Email">
                            <i class="bi bi-envelope"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-action" onclick="archiveAppointment(${apt.Appointment_ID})">
                            <i class="bi bi-archive"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
}

function viewAppointment(id) {
    const apt = appointments.find(a => a.Appointment_ID == id);
    
    if (!apt) {
        alert('Appointment not found!');
        return;
    }
    
    // Service Mode label
    let serviceModeText = 'N/A';
    if (apt.Service_Mode === 'home_service') {
        serviceModeText = '🏠 Home Service';
    } else if (apt.Service_Mode === 'store_service') {
        serviceModeText = '🏪 Store Service';
    }
    
    const modalBody = document.getElementById('viewModalBody');
    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6 mb-3">
                <strong>Appointment ID:</strong> #${apt.Appointment_ID}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Customer Name:</strong> ${apt.Customer_Name || 'N/A'}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Customer Email:</strong> ${apt.Customer_Email || 'N/A'}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Customer Contact:</strong> ${apt.Customer_Contact || 'N/A'}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Service:</strong> ${apt.Service_Name || 'N/A'}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Service Mode:</strong> ${serviceModeText}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Car Type:</strong> ${apt.Car_type}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Car Model:</strong> ${apt.Car_Model}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Date:</strong> ${apt.Appointment_date}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Time:</strong> ${apt.Appointment_time}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Location:</strong> ${apt.Location}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Status:</strong> <span class="badge badge-${apt.Status}">${apt.Status}</span>
            </div>
            <div class="col-md-6 mb-3">
                <strong>Created:</strong> ${apt.Created_at}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Updated:</strong> ${apt.Updated_at}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Handled By:</strong> ${apt.User_Name || 'N/A'}
            </div>
        </div>
    `;
}

function editAppointment(id) {
    const apt = appointments.find(a => a.Appointment_ID == id);
    
    if (!apt) {
        alert('Appointment not found!');
        return;
    }
    
    document.getElementById('editAppointmentId').value = id;
    document.getElementById('editDisplayId').value = '#' + apt.Appointment_ID;
    document.getElementById('editCustomerName').value = apt.Customer_Name || 'N/A';
    document.getElementById('editCurrentStatus').value = apt.Status.charAt(0).toUpperCase() + apt.Status.slice(1);
    document.getElementById('editStatus').value = apt.Status;
}

async function saveStatus() {
    const id = parseInt(document.getElementById('editAppointmentId').value);
    const oldStatus = document.getElementById('editCurrentStatus').value.toLowerCase();
    const newStatus = document.getElementById('editStatus').value;
    const customerName = document.getElementById('editCustomerName').value;
    
    if (oldStatus === newStatus) {
        alert('No changes made to status.');
        return;
    }
    
    const result = await updateAppointmentStatus(id, newStatus);
    
    if (result.success) {
        await logHistory(
            'Status Update',
            `Appointment #${id} (${customerName}) status changed from ${oldStatus} to ${newStatus}`
        );
        
        bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
        
        alert('Status updated successfully!');
        
        await updateStats();
        await fetchAppointments();
        await renderAppointmentsTable();
        await renderDashboardTable();
        
        if (calendar) {
            calendar.refetchEvents();
        }
    } else {
        alert('Error: ' + result.message);
    }
}

async function archiveAppointment(id) {
    const apt = appointments.find(a => a.Appointment_ID == id);
    
    if (!apt) {
        alert('Appointment not found!');
        return;
    }
    
    if (confirm('Archive this appointment?')) {
        const result = await deleteAppointment(id);
        
        if (result.success) {
            await logHistory(
                'Archive Appointment',
                `Appointment #${id} (${apt.Customer_Name}) has been archived`
            );
            
            await fetchAppointments();
            await updateStats();
            await renderAppointmentsTable();
            await renderDashboardTable();
            alert('Appointment archived successfully!');
        } else {
            alert('Error: ' + result.message);
        }
    }
}

async function applyFilters() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const service = document.getElementById('serviceFilter').value;
    
    const filters = {};
    if (status) filters.status = status;
    if (service) filters.service_id = service;
    if (search) filters.search = search;
    
    await fetchAppointments(filters);
    renderAppointmentsTable();
}

async function refreshTable() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('serviceFilter').value = '';
    await fetchAppointments();
    renderAppointmentsTable();
}

// ========== History Functions ==========
async function renderHistoryTable() {
    const history = await fetchHistory();
    const tbody = document.getElementById('historyTable');
    tbody.innerHTML = '';
    
    if (history.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">No history records</td></tr>';
        return;
    }
    
    history.forEach(record => {
        tbody.innerHTML += `
            <tr>
                <td>${new Date(record.Created_at).toLocaleString()}</td>
                <td>${record.User_Name || 'System'}</td>
                <td><span class="badge bg-primary">${record.Action}</span></td>
                <td>${record.Details}</td>
            </tr>
        `;
    });
}

// ========== Archive Functions ==========
async function renderArchiveTable() {
    const archive = await fetchArchive();
    const tbody = document.getElementById('archiveTable');
    tbody.innerHTML = '';
    
    if (archive.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center">No archived appointments</td></tr>';
        return;
    }
    
    archive.forEach(apt => {
        const badgeClass = `badge-${apt.Status}`;
        tbody.innerHTML += `
            <tr>
                <td>#${apt.Appointment_ID}</td>
                <td>${apt.Customer_Name}</td>
                <td>${apt.Service_Name || 'N/A'}</td>
                <td>${apt.Car_type}</td>
                <td>${apt.Car_Model}</td>
                <td>${apt.Appointment_date}</td>
                <td>${apt.Appointment_time}</td>
                <td><span class="badge ${badgeClass}">${apt.Status}</span></td>
                <td>${new Date(apt.Archived_at).toLocaleDateString()}</td>
                <td>
                    <button class="btn btn-success btn-sm" onclick="unarchive(${apt.Archive_ID})">
                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                    </button>
                </td>
            </tr>
        `;
    });
}

async function renderAccountsArchiveTable() {
    const archive = await fetchAccountsArchive();
    const tbody = document.getElementById('accountsArchiveTable');
    
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (archive.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No archived accounts</td></tr>';
        return;
    }
    
    archive.forEach(account => {
        tbody.innerHTML += `
            <tr>
                <td>${account.Username}</td>
                <td>${account.Email}</td>
                <td><span class="badge bg-info">${account.Role_Name}</span></td>
                <td>${account.Contact_Number || 'N/A'}</td>
                <td>${new Date(account.Archived_at).toLocaleDateString()}</td>
                <td>
                    <button class="btn btn-success btn-sm" onclick="unarchiveUserAccount(${account.Archive_ID})">
                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                    </button>
                </td>
            </tr>
        `;
    });
}

async function unarchive(archiveId) {
    if (confirm('Restore this appointment?')) {
        const result = await unarchiveAppointment(archiveId);
        
        if (result.success) {
            await logHistory(
                'Restore Appointment',
                `Appointment has been restored from archive`
            );
            
            await renderArchiveTable();
            await updateStats();
            alert('Appointment restored successfully!');
        } else {
            alert('Error: ' + result.message);
        }
    }
}

async function unarchiveUserAccount(archiveId) {
    if (confirm('Restore this account?')) {
        const result = await unarchiveAccount(archiveId);
        
        if (result.success) {
            await logHistory(
                'Restore Account',
                `User account has been restored from archive`
            );
            
            await renderAccountsArchiveTable();
            alert('Account restored successfully!');
        } else {
            alert('Error: ' + result.message);
        }
    }
}

// ========== Calendar Functions ==========
let calendar = null;

function initializeCalendar() {
    const calendarEl = document.getElementById('appointmentCalendar');
    
    if (!calendarEl) return;
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 'auto',
        displayEventTime: true,
        displayEventEnd: false,
        eventTimeFormat: {
            hour: 'numeric',
            minute: '2-digit',
            meridiem: 'short'
        },
        eventDisplay: 'block',
        events: async function(info, successCallback, failureCallback) {
            try {
                const start = info.startStr.split('T')[0];
                const end = info.endStr.split('T')[0];
                
                const response = await fetch(`${API_BASE_URL}/api_calendar.php?start=${start}&end=${end}`);
                const events = await response.json();
                successCallback(events);
            } catch (error) {
                console.error('Error loading calendar events:', error);
                failureCallback(error);
            }
        },
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        eventDidMount: function(info) {
            const props = info.event.extendedProps;
            info.el.title = `${props.customer}\n${props.service}\nTime: ${props.time}\nStatus: ${props.status}`;
        }
    });
    
    calendar.render();
}

function showEventDetails(event) {
    const props = event.extendedProps;
    
    const modalBody = `
        <div class="row">
            <div class="col-md-6 mb-3">
                <strong>Appointment ID:</strong> #${event.id}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Customer:</strong> ${props.customer}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Service:</strong> ${props.service}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Car:</strong> ${props.car}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Date:</strong> ${event.start.toLocaleDateString()}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Time:</strong> ${props.time}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Location:</strong> ${props.location}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Status:</strong> <span class="badge badge-${props.status}">${props.status}</span>
            </div>
        </div>
    `;
    
    document.getElementById('viewModalBody').innerHTML = modalBody;
    
    const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
    viewModal.show();
}

// ========== Accounts Functions ==========
async function renderAccounts() {
    await fetchUsers();
    const container = document.getElementById('accountsList');
    container.innerHTML = '';
    
    container.innerHTML = `
        <div class="table-card">
            <div class="table-card-header">
                Staff Accounts
            </div>
            <div class="table-card-body">
                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="accountsTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
    
    const tbody = document.getElementById('accountsTableBody');
    
    accounts.forEach(account => {
        const statusBadge = account.Status === 'active' 
            ? '<span class="badge bg-success">Active</span>' 
            : '<span class="badge bg-secondary">Deactivated</span>';
        
        const toggleBtn = account.Status === 'active'
            ? `<button class="btn btn-warning btn-sm btn-action" onclick="toggleAccountStatus(${account.User_ID}, 'disabled')">
                <i class="bi bi-pause-circle"></i> Deactivate
               </button>`
            : `<button class="btn btn-success btn-sm btn-action" onclick="toggleAccountStatus(${account.User_ID}, 'active')">
                <i class="bi bi-play-circle"></i> Activate
               </button>`;
        
        tbody.innerHTML += `
            <tr onclick="viewAccountDetails(${account.User_ID})" style="cursor: pointer;">
                <td>${account.Username}</td>
                <td>${account.Email}</td>
                <td>${account.Contact_Number || 'N/A'}</td>
                <td><span class="badge bg-info">${account.Role_Name || 'N/A'}</span></td>
                <td>${statusBadge}</td>
                <td onclick="event.stopPropagation()">
                    <div class="action-buttons">
                        ${toggleBtn}
                        <button class="btn btn-danger btn-sm btn-action" onclick="archiveAccountConfirm(${account.User_ID}, '${account.Username}')">
                            <i class="bi bi-archive"></i> Archive
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
}

async function addAccount() {
    const username = document.getElementById('staffName').value;
    const email = document.getElementById('staffEmail').value;
    const contact = document.getElementById('staffContact').value;
    const role = document.getElementById('staffRole').value;
    const password = document.getElementById('staffPassword').value;
    
    if (!username || !email || !contact || !password) {
        alert('Please fill all fields!');
        return;
    }
    
    const roleId = role === 'admin' ? 1 : 2;
    
    const userData = {
        Username: username,
        Email: email,
        Password: password,
        Contact_Number: contact,
        Role_ID: roleId,
        Status: 'active'
    };
    
    const result = await createUser(userData);
    
    if (result.success) {
        await logHistory(
            'Create Account',
            `New ${role} account created: ${username} (${email})`
        );
        
        await renderAccounts();
        document.getElementById('addAccountForm').reset();
        bootstrap.Modal.getInstance(document.getElementById('addAccountModal')).hide();
        alert('Account added successfully!');
    } else {
        alert('Error: ' + result.message);
    }
}

function viewAccountDetails(userId) {
    const account = accounts.find(a => a.User_ID == userId);
    
    if (!account) {
        alert('Account not found!');
        return;
    }
    
    const modalBody = document.getElementById('viewAccountModalBody');
    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6 mb-3">
                <strong>User ID:</strong> ${account.User_ID}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Username:</strong> ${account.Username}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Email:</strong> ${account.Email}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Contact Number:</strong> ${account.Contact_Number || 'N/A'}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Role:</strong> <span class="badge bg-info">${account.Role_Name || 'N/A'}</span>
            </div>
            <div class="col-md-6 mb-3">
                <strong>Status:</strong> <span class="badge ${account.Status === 'active' ? 'bg-success' : 'bg-secondary'}">${account.Status === 'active' ? 'Active' : 'Deactivated'}</span>
            </div>
            <div class="col-md-6 mb-3">
                <strong>Account Created:</strong> ${new Date(account.Created_at).toLocaleString()}
            </div>
        </div>
    `;
    
    const viewAccountModal = new bootstrap.Modal(document.getElementById('viewAccountModal'));
    viewAccountModal.show();
}

async function toggleAccountStatus(id, newStatus) {
    const statusText = newStatus === 'active' ? 'activate' : 'deactivate';
    
    if (confirm(`Are you sure you want to ${statusText} this account?`)) {
        const result = await toggleUserStatus(id, newStatus);
        
        if (result.success) {
            const action = newStatus === 'active' ? 'Activate Account' : 'Deactivate Account';
            await logHistory(
                action,
                `User account status changed to ${newStatus}`
            );
            
            await renderAccounts();
            alert(`Account ${statusText}d successfully!`);
        } else {
            alert('Error: ' + result.message);
        }
    }
}

async function archiveAccountConfirm(id, username) {
    if (confirm(`Archive account: ${username}?`)) {
        const result = await archiveUser(id);
        
        if (result.success) {
            await logHistory(
                'Archive Account',
                `User account ${username} has been archived`
            );
            
            await renderAccounts();
            alert('Account archived successfully!');
        } else {
            alert('Error: ' + result.message);
        }
    }
}

// ========== Email Functions ==========
async function sendEmailManual(appointmentId) {
    const apt = appointments.find(a => a.Appointment_ID == appointmentId);
    
    if (!apt) {
        alert('Appointment not found!');
        return;
    }
    
    if (!apt.Customer_Email) {
        alert('Customer email not available!');
        return;
    }
    
    const emailType = confirm('Send REMINDER email?\n\nClick OK for Reminder\nClick Cancel for Confirmation') 
        ? 'reminder' 
        : 'confirmation';
    
    if (confirm(`Send ${emailType} email to ${apt.Customer_Name} (${apt.Customer_Email})?`)) {
        try {
            const response = await fetch(`${API_BASE_URL}/api_send_email.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    Appointment_ID: appointmentId,
                    Email_Type: emailType
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('✓ Email sent successfully!');
                
                await logHistory(
                    'Send Email',
                    `${emailType.charAt(0).toUpperCase() + emailType.slice(1)} email sent to ${apt.Customer_Name} for Appointment #${appointmentId}`
                );
            } else {
                alert('✗ Error sending email: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('✗ Failed to send email. Please try again.');
        }
    }
}

// ========== Event Listeners ==========
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const serviceFilter = document.getElementById('serviceFilter');
    
    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    if (serviceFilter) serviceFilter.addEventListener('change', applyFilters);
    
    updateStats();
    renderDashboardTable();
    
    startAutoRefresh();
    
    console.log('Auto-refresh started. Dashboard will update every 30 seconds.');
});

document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopAutoRefresh();
        console.log('Auto-refresh paused (tab hidden)');
    } else {
        startAutoRefresh();
        console.log('Auto-refresh resumed (tab visible)');
    }
});