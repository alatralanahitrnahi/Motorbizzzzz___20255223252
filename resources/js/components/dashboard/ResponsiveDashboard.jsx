import React, { useState } from 'react';
import {
  Grid,
  Card,
  CardContent,
  Typography,
  Box,
  useMediaQuery,
  IconButton,
  Drawer,
  List,
  ListItem,
  ListItemIcon,
  ListItemText,
  Divider,
  AppBar,
  Toolbar,
  Button
} from '@mui/material';
import {
  Menu as MenuIcon,
  Dashboard as DashboardIcon,
  Gavel as GavelIcon,
  Warning as WarningIcon,
  Description as DescriptionIcon,
  Assignment as AssignmentIcon,
  CardMembership as CardMembershipIcon,
  Category as CategoryIcon,
  Report as ReportIcon,
  Business as BusinessIcon
} from '@mui/icons-material';
import { useTheme } from '@mui/material/styles';
import { Link, useLocation } from 'react-router-dom';

const ResponsiveDashboard = ({ children }) => {
  const theme = useTheme();
  const isMobile = useMediaQuery(theme.breakpoints.down('md'));
  const [mobileOpen, setMobileOpen] = useState(false);
  const location = useLocation();

  const handleDrawerToggle = () => {
    setMobileOpen(!mobileOpen);
  };

  const menuItems = [
    { text: 'Dashboard', icon: <DashboardIcon />, path: '/' },
    { 
      text: 'Compliance', 
      icon: <GavelIcon />, 
      path: null, 
      children: [
        { text: 'Requirements', icon: <DescriptionIcon />, path: '/compliance/requirements' },
        { text: 'Documents', icon: <DescriptionIcon />, path: '/compliance/documents' },
        { text: 'Audits', icon: <AssignmentIcon />, path: '/compliance/audits' },
        { text: 'Certificates & Licenses', icon: <CardMembershipIcon />, path: '/compliance/certificates' },
      ]
    },
    { 
      text: 'Risk Management', 
      icon: <WarningIcon />, 
      path: null, 
      children: [
        { text: 'Categories', icon: <CategoryIcon />, path: '/risk/categories' },
        { text: 'Assessments', icon: <WarningIcon />, path: '/risk/assessments' },
        { text: 'Incidents', icon: <ReportIcon />, path: '/risk/incidents' },
        { text: 'Business Continuity', icon: <BusinessIcon />, path: '/risk/continuity' },
      ]
    },
  ];

  const drawer = (
    <div>
      <Toolbar>
        <Typography variant="h6" noWrap component="div">
          MonitorBizz
        </Typography>
      </Toolbar>
      <Divider />
      <List>
        {menuItems.map((item, index) => (
          <React.Fragment key={index}>
            <ListItem 
              button 
              component={item.path ? Link : 'div'} 
              to={item.path} 
              selected={item.path && location.pathname === item.path}
            >
              <ListItemIcon>
                {item.icon}
              </ListItemIcon>
              <ListItemText primary={item.text} />
            </ListItem>
            {item.children && (
              <List component="div" disablePadding>
                {item.children.map((child, childIndex) => (
                  <ListItem 
                    key={childIndex} 
                    button 
                    component={Link} 
                    to={child.path} 
                    selected={location.pathname === child.path}
                    sx={{ pl: 4 }}
                  >
                    <ListItemIcon>
                      {child.icon}
                    </ListItemIcon>
                    <ListItemText primary={child.text} />
                  </ListItem>
                ))}
              </List>
            )}
          </React.Fragment>
        ))}
      </List>
    </div>
  );

  const container = window !== undefined ? () => window.document.body : undefined;

  return (
    <Box sx={{ display: 'flex' }}>
      <AppBar
        position="fixed"
        sx={{
          [theme.breakpoints.up('md')]: {
            width: `calc(100% - 240px)`,
            ml: `240px`,
          },
        }}
      >
        <Toolbar>
          {isMobile && (
            <IconButton
              color="inherit"
              aria-label="open drawer"
              edge="start"
              onClick={handleDrawerToggle}
              sx={{ mr: 2, [theme.breakpoints.up('md')]: { display: 'none' } }}
            >
              <MenuIcon />
            </IconButton>
          )}
          <Typography variant="h6" noWrap component="div">
            Compliance & Risk Management
          </Typography>
        </Toolbar>
      </AppBar>
      
      <Box
        component="nav"
        sx={{ width: { md: 240 }, flexShrink: { md: 0 } }}
        aria-label="navigation"
      >
        {/* The implementation can be swapped with js to avoid SEO duplication of links. */}
        {isMobile ? (
          <Drawer
            container={container}
            variant="temporary"
            open={mobileOpen}
            onClose={handleDrawerToggle}
            ModalProps={{
              keepMounted: true, // Better open performance on mobile.
            }}
            sx={{
              '& .MuiDrawer-paper': { boxSizing: 'border-box', width: 240 },
            }}
          >
            {drawer}
          </Drawer>
        ) : (
          <Drawer
            variant="permanent"
            sx={{
              '& .MuiDrawer-paper': { boxSizing: 'border-box', width: 240 },
            }}
            open
          >
            {drawer}
          </Drawer>
        )}
      </Box>
      
      <Box
        component="main"
        sx={{
          flexGrow: 1,
          p: 3,
          width: { md: `calc(100% - 240px)` },
          mt: 8
        }}
      >
        {children}
      </Box>
    </Box>
  );
};

export default ResponsiveDashboard;