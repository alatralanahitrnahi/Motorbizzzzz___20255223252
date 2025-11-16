import React from 'react';
import { Drawer, List, ListItem, ListItemIcon, ListItemText, Toolbar, Divider, Typography } from '@mui/material';
import { 
  Dashboard as DashboardIcon,
  Gavel as GavelIcon,
  Description as DescriptionIcon,
  Assignment as AssignmentIcon,
  CardMembership as CardMembershipIcon,
  Category as CategoryIcon,
  Warning as WarningIcon,
  Report as ReportIcon,
  Business as BusinessIcon
} from '@mui/icons-material';
import { Link, useLocation } from 'react-router-dom';

const drawerWidth = 240;

const Navigation = () => {
  const location = useLocation();

  const menuItems = [
    { text: 'Dashboard', icon: <DashboardIcon />, path: '/' },
    { text: 'Compliance', icon: <GavelIcon />, path: null, children: [
      { text: 'Requirements', icon: <DescriptionIcon />, path: '/compliance/requirements' },
      { text: 'Documents', icon: <DescriptionIcon />, path: '/compliance/documents' },
      { text: 'Audits', icon: <AssignmentIcon />, path: '/compliance/audits' },
      { text: 'Certificates & Licenses', icon: <CardMembershipIcon />, path: '/compliance/certificates' },
    ]},
    { text: 'Risk Management', icon: <WarningIcon />, path: null, children: [
      { text: 'Categories', icon: <CategoryIcon />, path: '/risk/categories' },
      { text: 'Assessments', icon: <WarningIcon />, path: '/risk/assessments' },
      { text: 'Incidents', icon: <ReportIcon />, path: '/risk/incidents' },
      { text: 'Business Continuity', icon: <BusinessIcon />, path: '/risk/continuity' },
    ]},
  ];

  const isActive = (path) => {
    return location.pathname === path;
  };

  return (
    <Drawer
      variant="permanent"
      sx={{
        width: drawerWidth,
        flexShrink: 0,
        [`& .MuiDrawer-paper`]: { width: drawerWidth, boxSizing: 'border-box' },
      }}
    >
      <Toolbar>
        <Typography variant="h6" noWrap component="div">
          MonitorBizz
        </Typography>
      </Toolbar>
      <Divider />
      <List>
        {menuItems.map((item, index) => (
          <React.Fragment key={index}>
            <ListItem button component={item.path ? Link : 'div'} to={item.path} selected={item.path && isActive(item.path)}>
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
                    selected={isActive(child.path)}
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
    </Drawer>
  );
};

export default Navigation;