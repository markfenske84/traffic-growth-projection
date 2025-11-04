# Database Upgrade Instructions

If you're experiencing "Unknown column 'sort_order'" errors after updating the plugin, you need to manually upgrade the database.

## Quick Fix (Recommended)

**Deactivate and Reactivate the Plugin:**

1. Go to WordPress Admin → Plugins
2. Click "Deactivate" on Traffic Growth Projection
3. Click "Activate" on Traffic Growth Projection
4. The database will be automatically upgraded

## Alternative Fix (Using WP-CLI)

If you have WP-CLI access, run:

```bash
wp plugin deactivate traffic-growth-projection
wp plugin activate traffic-growth-projection
```

## What This Does

The reactivation runs the database upgrade routine that adds the `sort_order` column needed for drag-and-drop project reordering.

## Manual SQL Fix (Last Resort)

If the above doesn't work, run this SQL query in your database (via phpMyAdmin or similar):

```sql
ALTER TABLE wp_tgp_projects 
ADD COLUMN sort_order int(11) DEFAULT 0 AFTER cltv,
ADD INDEX sort_order (sort_order);
```

Note: Replace `wp_` with your actual database prefix if different.

## After Upgrade

Once the column is added, the plugin will work normally:
- Projects can be drag-and-drop reordered
- New projects can be created
- All functionality will be restored

