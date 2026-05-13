# UA Facility Management System - Windows Launcher

## 🎯 Purpose
This Windows launcher provides secure access to the hidden login portal for authorized staff only.

## 🔧 How It Works
1. Prompts for a secret access key
2. Validates the key locally
3. Opens the hidden login URL in default browser
4. Only authorized personnel with the launcher and key can access

## 📦 Building the Executable

### Prerequisites
- Python installed on your development machine
- Command line/terminal access

### Build Steps

1. **Install PyInstaller** (one-time setup):
```bash
pip install pyinstaller
```

2. **Build the executable**:
```bash
cd "C:\Users\user\Desktop\Dumps\Capstone Univerity Of Antique Facility Management System"
pyinstaller --onefile launcher.py
```

3. **Find the executable**:
- Look in: `dist/launcher.exe`
- This is the file you distribute to staff/admins

## 🚀 Using the Launcher

### For Staff/Admins
1. Double-click `launcher.exe`
2. Enter the access key when prompted
3. Browser opens with login page
4. Login with your credentials

### Current Access Key
```
UA-FMS-2025
```

### Login Credentials (for testing)
- **Email**: admin@example.com
- **Password**: password123

## 🔒 Security Features

### Hidden Login URL
- **URL**: `/fms-portal-entry`
- **Not linked anywhere** in the public interface
- **Only accessible** through launcher or direct URL knowledge

### Dual Authentication
1. **Access Key**: Validates user can reach login
2. **User Credentials**: Standard Laravel authentication
3. **Role-Based Access**: Middleware enforces dashboard permissions

## 🌐 URLs

### Development Environment
- **Public**: `http://127.0.0.1:8000/`
- **Login**: `http://127.0.0.1:8000/fms-portal-entry`

### Production (when deployed)
- Update `LOGIN_URL` in `launcher.py` before building:
```python
LOGIN_URL = "https://fms.uaniversity.edu/fms-portal-entry"
```

## 🎛️ Customization

### Change Access Key
Edit `SECRET` in `launcher.py`:
```python
SECRET = "YOUR_NEW_SECRET_KEY"
```

### Role-Specific Launchers (optional)
Create separate launchers for different roles:
```python
# Admin launcher
LOGIN_URL = "http://127.0.0.1:8000/fms-portal-entry"
SECRET = "UA-FMS-ADMIN-2025"

# College staff launcher  
LOGIN_URL = "http://127.0.0.1:8000/fms-portal-entry"
SECRET = "UA-FMS-COLLEGE-2025"

# Org staff launcher
LOGIN_URL = "http://127.0.0.1:8000/fms-portal-entry"
SECRET = "UA-FMS-ORG-2025"
```

## 📋 Distribution Checklist

When distributing to staff:
- [ ] Provide `launcher.exe` file
- [ ] Share the current access key
- [ ] Include login credentials for testing
- [ ] Explain the dual authentication process
- [ ] Provide support contact information

## 🛡️ Additional Security (Future Enhancements)

### Optional Hardening
- **HTTPS**: Use SSL certificates in production
- **Network Access**: Restrict to campus network via firewall
- **Token Validation**: Add query parameter validation
- **Logging**: Track access attempts

### Example Token Enhancement
```python
LOGIN_URL = "http://127.0.0.1:8000/fms-portal-entry?access_token=PRE_SHARED_TOKEN"
```

## 📞 Support

If staff encounter issues:
1. Verify they have the correct access key
2. Check if Laravel server is running
3. Confirm browser can access the URL
4. Contact system administrator for assistance

---

**Note**: This launcher adds convenience and an extra security layer, but the primary security remains Laravel's authentication and role-based access control.
