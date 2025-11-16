import apiService from './apiService';

class AuthService {
  // Login user
  async login(email, password) {
    try {
      const response = await apiService.post('/login', {
        email,
        password
      });
      
      if (response.token) {
        // Store token in localStorage
        localStorage.setItem('authToken', response.token);
        // Store user data if provided
        if (response.user) {
          localStorage.setItem('user', JSON.stringify(response.user));
        }
        return response;
      } else {
        throw new Error('Login failed: No token received');
      }
    } catch (error) {
      throw new Error(error.message || 'Login failed');
    }
  }

  // Register new user and business
  async register(userData) {
    try {
      const response = await apiService.post('/register', userData);
      
      if (response.token) {
        // Store token in localStorage
        localStorage.setItem('authToken', response.token);
        // Store user data if provided
        if (response.user) {
          localStorage.setItem('user', JSON.stringify(response.user));
        }
        return response;
      } else {
        throw new Error('Registration failed: No token received');
      }
    } catch (error) {
      throw new Error(error.message || 'Registration failed');
    }
  }

  // Logout user
  logout() {
    // Remove token and user data from localStorage
    localStorage.removeItem('authToken');
    localStorage.removeItem('user');
    
    // Redirect to login page
    window.location.href = '/login';
  }

  // Get current user
  getCurrentUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  }

  // Get auth token
  getToken() {
    return localStorage.getItem('authToken');
  }

  // Check if user is authenticated
  isAuthenticated() {
    const token = this.getToken();
    if (!token) return false;
    
    // Check if token is expired (simplified check)
    try {
      const payload = JSON.parse(atob(token.split('.')[1]));
      const currentTime = Date.now() / 1000;
      return payload.exp > currentTime;
    } catch (error) {
      return false;
    }
  }

  // Check user permissions
  hasPermission(permission) {
    const user = this.getCurrentUser();
    if (!user || !user.permissions) return false;
    
    return user.permissions.includes(permission);
  }

  // Check if user has role
  hasRole(role) {
    const user = this.getCurrentUser();
    if (!user || !user.roles) return false;
    
    return user.roles.includes(role);
  }

  // Refresh auth token
  async refreshToken() {
    try {
      const response = await apiService.post('/refresh-token');
      if (response.token) {
        localStorage.setItem('authToken', response.token);
        return response.token;
      }
    } catch (error) {
      // If refresh fails, logout user
      this.logout();
      throw new Error('Session expired. Please login again.');
    }
  }

  // Update user profile
  async updateProfile(userData) {
    try {
      const response = await apiService.put('/me', userData);
      if (response.user) {
        localStorage.setItem('user', JSON.stringify(response.user));
      }
      return response;
    } catch (error) {
      throw new Error(error.message || 'Failed to update profile');
    }
  }

  // Change password
  async changePassword(currentPassword, newPassword) {
    try {
      const response = await apiService.post('/change-password', {
        current_password: currentPassword,
        new_password: newPassword
      });
      return response;
    } catch (error) {
      throw new Error(error.message || 'Failed to change password');
    }
  }
}

// Export a singleton instance
export default new AuthService();