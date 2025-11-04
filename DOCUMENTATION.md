# Traffic Growth Projection Plugin
## Complete User & Developer Documentation

---

## Table of Contents

1. [Introduction](#introduction)
2. [What This Plugin Does](#what-this-plugin-does)
3. [Getting Started](#getting-started)
4. [Core Features](#core-features)
5. [Step-by-Step Usage Guide](#step-by-step-usage-guide)
6. [Understanding Your Data](#understanding-your-data)
7. [Sharing Results with Clients](#sharing-results-with-clients)
8. [Plugin Updates](#plugin-updates)
9. [Development & Contribution Guidelines](#development--contribution-guidelines)

---

## Introduction

Traffic Growth Projection is a WordPress plugin designed to help SEO professionals, digital marketing agencies, and website owners forecast traffic growth based on keyword rankings. It provides visual projections, ROI calculations, and client-friendly reports—all from within your WordPress dashboard.

**Current Version:** 1.0.3  
**Requires:** WordPress 5.0+, PHP 7.4+  
**License:** GPL v2 or later

---

## What This Plugin Does

This plugin helps you answer critical business questions:

- **"How much traffic will we get if we rank for these keywords?"**  
  Import your keyword list and see month-by-month traffic projections over 12 months.

- **"What's the ROI of our SEO investment?"**  
  Set conversion rates and customer lifetime values to calculate potential revenue and return on investment.

- **"How do we compare current performance vs. future potential?"**  
  Visualize the gap between where you are now and where you could be with improved rankings.

- **"How do we show clients their progress?"**  
  Generate clean, professional reports that can be embedded on any WordPress page using shortcodes.

### Key Capabilities

✅ Create multiple projects for different clients or websites  
✅ Import keyword data from CSV files  
✅ Automatic traffic calculations based on ranking positions  
✅ Interactive charts showing 12-month projections  
✅ Compare different keyword strategies (existing, must-have, new)  
✅ ROI calculator with conversion tracking  
✅ Export reports as CSV  
✅ Client-facing displays with shortcodes  
✅ Gutenberg block support  
✅ Automatic updates from GitHub

---

## Getting Started

### Installation

1. **Upload the Plugin**
   - Download the plugin folder
   - Upload to `/wp-content/plugins/traffic-growth-projection`
   - Or upload via WordPress admin: Plugins → Add New → Upload Plugin

2. **Activate the Plugin**
   - Go to Plugins → Installed Plugins
   - Find "Traffic Growth Projection"
   - Click "Activate"

3. **Access the Plugin**
   - Look for "Traffic Growth" in your WordPress admin menu (left sidebar)
   - You'll see a chart icon 📈

### First-Time Setup

When you first open the plugin, you'll see an empty dashboard. Here's what to do:

1. Click "**Download Import Template**" to get the sample CSV file
2. Click "**Create Your First Project**" to set up your first projection
3. Fill in the project details (don't worry, you can change these later)
4. Import your keyword data using the template

That's it! You're ready to start projecting traffic.

---

## Core Features

### 1. Project Management

**What it is:** A project is a collection of keywords, projections, and settings for a specific website or campaign.

**What you can do:**
- Create unlimited projects
- Set custom conversion rates for each project
- Define customer lifetime value (CLTV)
- Add descriptions to keep projects organized
- Reorder projects by dragging and dropping cards

### 2. CSV Keyword Import

**What it is:** Quickly add hundreds of keywords to your project by uploading a spreadsheet.

**What you can do:**
- Import keywords with search volume, difficulty, rankings, and categories
- Use the provided template to ensure correct formatting
- Import multiple files to the same project
- Edit keywords individually after import

### 3. Traffic Projections

**What it is:** Automatic calculations showing how much traffic you'll receive based on keyword rankings over 12 months.

**What you can do:**
- View four different projection scenarios
- Toggle projections on/off in the interactive chart
- See month-by-month growth trends
- Compare current performance vs. projected growth

### 4. ROI Calculator

**What it is:** A tool that converts traffic numbers into business metrics—conversions, revenue, and return on investment.

**What you can do:**
- Enter your monthly SEO investment
- See projected conversions (low and high estimates)
- Calculate expected revenue based on CLTV
- Determine your ROI multiplier

### 5. Keyword Management

**What it is:** A filterable table showing all keywords in your project with their traffic potential.

**What you can do:**
- Filter keywords by category
- Edit individual keywords
- Delete keywords that are no longer relevant
- Export keyword data as CSV

### 6. Client-Facing Reports

**What it is:** Beautiful, public-facing displays of your projections that you can share with clients.

**What you can do:**
- Embed projections on any WordPress page or post
- Show clients their growth potential without giving them admin access
- Use the Gutenberg block for easy insertion
- Customize which project displays where

---

## Step-by-Step Usage Guide

### Creating Your First Project

**Step 1: Open the Plugin**
- Navigate to **Traffic Growth** in your WordPress admin menu

**Step 2: Create a New Project**
- Click the **"Create New Project"** button
- You'll see a popup form

**Step 3: Fill in Project Details**
- **Project Name:** Give it a clear name (e.g., "ABC Company SEO Campaign")
- **Description:** Optional notes about the project
- **Conversion Rate Low:** Conservative estimate (default: 1%)
- **Conversion Rate High:** Optimistic estimate (default: 5%)
- **Customer Lifetime Value:** How much each customer is worth to the business

**Step 4: Save the Project**
- Click **"Save Project"**
- You'll see your new project card on the dashboard

### Importing Keywords

**Step 1: Download the Template**
- Click **"Download Import Template"** to get the sample CSV file
- Open it in Excel, Google Sheets, or any spreadsheet program

**Step 2: Prepare Your Data**

Your CSV must have these columns (in this order):

| Column A | Column B | Column C | Column D | Column E | Column F | Column G |
|----------|----------|----------|----------|----------|----------|----------|
| Keyword | Search Volume | Difficulty | Estimated Traffic | Current Ranking | Expected Ranking | Category |

**Column Details:**

- **Keyword:** The search term (e.g., "dog training tips")
- **Search Volume:** Monthly searches for this keyword (e.g., 5400)
- **Difficulty:** How hard to rank, 0-100 (e.g., 45)
- **Estimated Traffic:** Potential monthly visits if ranked #1 (e.g., 1200)
- **Current Ranking:** Where you rank today (0 if not ranking, 1-100 if ranking)
- **Expected Ranking:** Where you expect to rank (1-10 only)
- **Category:** One of these exact options:
  - `Existing Keywords (transactional terms only)`
  - `Must-Have Keywords (limit to 50)`
  - `New Keywords (limit to 100)`

**Step 3: Import Your File**
- Open your project by clicking **"View Project"**
- Click **"Import Keywords"**
- Select your CSV file
- Click **"Upload and Import"**
- Wait for the success message

### Viewing Your Projections

Once keywords are imported, you'll see:

**1. Interactive Chart**
- Shows traffic growth over 12 months
- Click legend items to toggle projections on/off
- Four different views:
  - **Current Trajectory:** Where you're headed if nothing changes (flat line)
  - **Existing Keywords:** Growth from improving current rankings
  - **Must-Have Keywords:** Essential terms you need to rank for
  - **New Keywords:** Additional opportunities

**2. Summary Stats**
- Current monthly traffic
- Projected monthly traffic (at month 12)
- Percentage growth
- Total keywords in project

**3. Chart Controls**
- Export button to download data as CSV
- Toggle buttons to show/hide specific projections

### Using the ROI Calculator

**Step 1: Scroll to the ROI Section**
- Below the chart, you'll find the "ROI Calculator"

**Step 2: Enter Your Investment**
- Type your monthly SEO spend (e.g., $5,000)
- This is what you're paying for SEO services, tools, content, etc.

**Step 3: Review Results**
- **Traffic:** Projected monthly visitors at month 12
- **Conversions (Low/High):** Expected customers based on your conversion rates
- **Revenue (Low/High):** Total value based on CLTV
- **ROI (Low/High):** Your return on investment
  - Example: 5.2x means you get $5.20 back for every $1 invested

### Managing Keywords

**Step 1: Scroll to Keywords Table**
- Below the ROI calculator

**Step 2: Filter by Category** (optional)
- Use the dropdown to show only certain keyword types

**Step 3: Edit or Delete Keywords**
- Click **"Edit"** to modify a keyword's data
- Click **"Delete"** to remove it from the project
- Changes update projections immediately

**Step 4: Export Keywords**
- Click **"Export Report"** to download as CSV
- Open in Excel for further analysis

---

## Understanding Your Data

### How Traffic is Calculated

The plugin uses industry-standard "capture rates" based on ranking position:

| Ranking Position | Traffic Captured |
|------------------|------------------|
| Position 1 | 60% of estimated traffic |
| Position 2 | 50% of estimated traffic |
| Position 3 | 40% of estimated traffic |
| Position 4 | 30% of estimated traffic |
| Position 5 | 25% of estimated traffic |
| Position 6 | 20% of estimated traffic |
| Position 7 | 15% of estimated traffic |
| Position 8 | 12% of estimated traffic |
| Position 9 | 8% of estimated traffic |
| Position 10 | 5% of estimated traffic |
| Position 11+ | 0% (not tracked) |

**Example:**
- Keyword: "dog training tips"
- Estimated Traffic: 1,200 visits/month
- Expected Ranking: Position 3
- **Projected Traffic:** 1,200 × 40% = **480 visits/month**

### Growth Over Time

Projections show gradual growth over 12 months:
- Month 1: ~8% of full potential
- Month 6: ~50% of full potential
- Month 12: ~100% of full potential

This models realistic SEO timelines where rankings improve gradually.

### Keyword Categories Explained

**Current Trajectory**
- Uses your *current* rankings to show where you're headed without any changes
- Usually a flat or slightly declining line
- Represents "baseline" performance

**Existing Keywords (transactional terms only)**
- Keywords you already rank for (position 1-100)
- Focus on transactional/commercial intent (e.g., "buy," "best," "review")
- Shows potential from improving existing rankings

**Must-Have Keywords (limit to 50)**
- High-priority terms essential to your business
- Usually high-volume or high-value keywords
- Limit to your top 50 targets

**New Keywords (limit to 100)**
- Opportunities you don't currently rank for
- Expansion terms, long-tail variations
- Limit to your top 100 new targets

### ROI Metrics Explained

**Conversion Rate**
- Percentage of visitors who become customers
- "Low" = conservative estimate
- "High" = optimistic estimate
- Typical range: 1-5% for most businesses

**Customer Lifetime Value (CLTV)**
- Total revenue from one customer over their lifetime
- Example: If customers spend $100/year and stay 3 years, CLTV = $300
- Use your actual business data for accuracy

**ROI Multiplier**
- Shows return on investment as a multiplier
- Example: 3.5x means you get $3.50 for every $1 invested
- Formula: Revenue ÷ Investment = ROI

---

## Sharing Results with Clients

### Using Shortcodes

**Step 1: Get Your Shortcode**
- On the dashboard, each project card shows its shortcode
- Example: `[traffic_projection id="1"]`
- Click the 📋 icon to copy it

**Step 2: Add to a Page**
- Edit any WordPress page or post
- Paste the shortcode where you want the projection to appear
- Publish the page

**Step 3: Share with Client**
- Send them the page URL
- They'll see a clean, professional view with:
  - Project summary stats
  - Interactive traffic chart
  - ROI calculations
  - No admin controls (read-only)

### Using the Gutenberg Block

**Step 1: Edit a Page**
- Open the page editor (Gutenberg)

**Step 2: Add the Block**
- Click the **+** button to add a new block
- Search for "Traffic Projection"
- Click to insert

**Step 3: Select a Project**
- Choose which project to display from the dropdown
- Preview and publish

### Reordering Projects

**Step 1: Open Dashboard**
- Navigate to **Traffic Growth** in admin menu

**Step 2: Drag and Drop**
- Hover over a project card
- Click and hold the **⋮⋮** drag handle
- Drag the card to a new position
- Release to drop

Your custom order is saved automatically.

---

## Plugin Updates

This plugin uses **GitHub** for updates, which means you'll receive automatic update notifications in WordPress just like any other plugin.

### How Updates Work

1. **Automatic Checking**
   - WordPress checks the GitHub repository every 12 hours
   - If a new version is available, you'll see an update notification

2. **Update Notification**
   - Go to **Plugins → Installed Plugins**
   - You'll see "Update Available" under Traffic Growth Projection
   - Or check **Dashboard → Updates**

3. **Installing Updates**
   - Click **"Update Now"**
   - The plugin downloads directly from GitHub
   - Your data (projects, keywords) is preserved
   - Takes 5-10 seconds

### Manual Update Check

If you want to check immediately:
1. Go to **Dashboard → Updates**
2. Click **"Check Again"** at the top
3. Any available updates will appear

### What Gets Updated

- New features and functionality
- Bug fixes and improvements
- Security patches
- Performance enhancements

### What Stays the Same

- All your projects and data
- All your keywords
- All your settings
- Your shortcodes (still work the same)

### Version History

You can view all past versions and release notes at:
**https://github.com/markfenske84/traffic-growth-projection**

---

## Development & Contribution Guidelines

*This section is for developers who want to contribute to or customize the plugin.*

### Repository Information

**GitHub Repository:** https://github.com/markfenske84/traffic-growth-projection  
**Clone via SSH:** `git@github.com:markfenske84/traffic-growth-projection.git`  
**Clone via HTTPS:** `https://github.com/markfenske84/traffic-growth-projection.git`

### Getting Started with Development

**1. Clone the Repository**
```bash
git clone git@github.com:markfenske84/traffic-growth-projection.git
cd traffic-growth-projection
```

**2. Install in WordPress**
- Place the cloned folder in `/wp-content/plugins/`
- Activate the plugin in WordPress admin

**3. Create a Development Branch**
```bash
git checkout -b feature/your-feature-name
```

### Project Structure

```
traffic-growth-projection/
├── assets/
│   ├── css/          # Stylesheets (admin, frontend, block editor)
│   └── js/           # JavaScript files (admin, frontend, chart library)
├── includes/
│   ├── class-tgp-admin.php       # Admin interface & AJAX handlers
│   ├── class-tgp-blocks.php      # Gutenberg block registration
│   ├── class-tgp-calculator.php  # Traffic & ROI calculations
│   ├── class-tgp-database.php    # Database operations
│   ├── class-tgp-frontend.php    # Shortcode & frontend display
│   └── class-tgp-importer.php    # CSV import handling
├── templates/
│   ├── dashboard.php             # Admin dashboard view
│   ├── project-view.php          # Admin project detail view
│   └── frontend-view.php         # Client-facing display
├── plugin-update-checker/        # GitHub update library
├── traffic-growth-projection.php # Main plugin file
├── readme.txt                    # WordPress.org readme
├── sample-data.csv              # CSV import template
└── .gitignore                   # Git ignore rules
```

### WordPress Coding Standards

This plugin follows [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/). Please ensure your code adheres to these guidelines:

- Use WordPress functions over PHP native functions when available
- Sanitize all input: `sanitize_text_field()`, `sanitize_email()`, etc.
- Escape all output: `esc_html()`, `esc_url()`, `esc_attr()`, etc.
- Use nonces for security: `wp_create_nonce()`, `wp_verify_nonce()`
- Prefix all functions and classes with `tgp_` or `TGP_`
- Use single quotes for strings unless interpolation is needed

### Database Schema

**Projects Table:** `{prefix}tgp_projects`
- `id` (bigint) - Primary key
- `name` (varchar 255) - Project name
- `description` (text) - Project description
- `conversion_rate_low` (decimal 5,2) - Low conversion percentage
- `conversion_rate_high` (decimal 5,2) - High conversion percentage
- `cltv` (decimal 10,2) - Customer lifetime value
- `display_order` (int) - Sort order for dashboard
- `created_at` (datetime) - Creation timestamp
- `updated_at` (datetime) - Last update timestamp

**Keywords Table:** `{prefix}tgp_keywords`
- `id` (bigint) - Primary key
- `project_id` (bigint) - Foreign key to projects
- `keyword` (varchar 255) - Keyword text
- `search_volume` (int) - Monthly search volume
- `difficulty` (int) - Difficulty score 0-100
- `estimated_traffic` (int) - Potential monthly visits
- `current_ranking` (int) - Current position (0 if not ranking)
- `expected_ranking` (int) - Target position
- `category` (varchar 100) - Keyword category
- `created_at` (datetime) - Creation timestamp

### AJAX Endpoints

All AJAX calls use WordPress's admin-ajax.php with these actions:

| Action | Function | Purpose |
|--------|----------|---------|
| `tgp_create_project` | `TGP_Admin::ajax_create_project()` | Create new project |
| `tgp_update_project` | `TGP_Admin::ajax_update_project()` | Update project details |
| `tgp_delete_project` | `TGP_Admin::ajax_delete_project()` | Delete a project |
| `tgp_import_keywords` | `TGP_Admin::ajax_import_keywords()` | Import CSV keywords |
| `tgp_get_projections` | `TGP_Admin::ajax_get_projections()` | Fetch projection data |
| `tgp_get_keywords` | `TGP_Admin::ajax_get_keywords()` | Fetch keywords list |
| `tgp_calculate_roi` | `TGP_Admin::ajax_calculate_roi()` | Calculate ROI metrics |
| `tgp_update_project_order` | `TGP_Admin::ajax_update_project_order()` | Save drag-drop order |
| `tgp_update_keyword` | `TGP_Admin::ajax_update_keyword()` | Edit a keyword |
| `tgp_delete_keyword` | `TGP_Admin::ajax_delete_keyword()` | Delete a keyword |
| `tgp_get_keyword` | `TGP_Admin::ajax_get_keyword()` | Fetch single keyword |

All endpoints:
- Require `manage_options` capability
- Verify nonce: `tgp_nonce`
- Return JSON responses

### Making Changes

**1. Feature Development**
- Create a feature branch: `feature/add-new-calculation`
- Make your changes
- Test thoroughly in a local WordPress install
- Commit with clear messages

**2. Bug Fixes**
- Create a bugfix branch: `bugfix/fix-csv-import`
- Reproduce the bug
- Fix and test
- Commit with reference to the issue

**3. Committing Best Practices**

Good commit messages:
```
Add export functionality to ROI calculator

- Added "Export ROI" button to calculator section
- Implemented CSV generation with projections data
- Sanitized all output data
- Added user permission checks
```

Bad commit messages:
```
fix stuff
updates
changes to admin.js
```

### Submitting Changes (Pull Requests)

**Step 1: Push Your Branch**
```bash
git add .
git commit -m "Add detailed description of changes"
git push origin feature/your-feature-name
```

**Step 2: Create Pull Request on GitHub**
- Go to https://github.com/markfenske84/traffic-growth-projection
- Click **"Pull Requests"** tab
- Click **"New Pull Request"**
- Select your branch
- Fill in the template:
  - **Title:** Clear, concise summary
  - **Description:** What changed and why
  - **Testing:** How you tested the changes
  - **Screenshots:** If UI changes

**Step 3: Code Review**
- Maintainer will review your code
- You may receive feedback or change requests
- Make any requested changes
- Push updates to the same branch (PR updates automatically)

**Step 4: Merge**
- Once approved, maintainer will merge
- Your code becomes part of the next release
- Delete your feature branch after merge

### Testing Your Changes

Before submitting a PR, test:

1. **Fresh Install**
   - Deactivate and delete plugin
   - Install your version
   - Activate and create a test project

2. **Existing Data**
   - Ensure changes don't break existing projects
   - Test with real keyword data
   - Verify exports still work

3. **Edge Cases**
   - Empty projects
   - Projects with 1000+ keywords
   - Invalid CSV imports
   - Permission checks (logged-out users)

4. **Cross-Browser**
   - Chrome, Firefox, Safari
   - Test JavaScript functionality
   - Verify charts render correctly

5. **WordPress Versions**
   - Test on minimum required version (5.0)
   - Test on latest WordPress version

### Versioning

This plugin uses [Semantic Versioning](https://semver.org/):

- **Major (1.x.x):** Breaking changes
- **Minor (x.1.x):** New features, backward-compatible
- **Patch (x.x.1):** Bug fixes, backward-compatible

**When releasing a new version:**

1. Update version in `traffic-growth-projection.php` (line 6)
2. Update version constant (line 23): `TGP_VERSION`
3. Update `readme.txt` changelog
4. Commit with message: `Release version X.X.X`
5. Push to main branch
6. Create GitHub release (optional, for release notes)

### Release Checklist

Before releasing a new version:

- [ ] All features tested and working
- [ ] No console errors in browser
- [ ] No PHP errors or warnings
- [ ] Version numbers updated in all files
- [ ] Changelog updated in readme.txt
- [ ] Database schema changes include upgrade routine
- [ ] Backward compatibility maintained
- [ ] Commit pushed to GitHub
- [ ] Plugin tested on live WordPress site

### Issue Reporting

**Found a bug?** Report it on GitHub:

1. Go to **Issues** tab
2. Click **"New Issue"**
3. Choose "Bug Report" template
4. Fill in:
   - Clear title
   - Steps to reproduce
   - Expected behavior
   - Actual behavior
   - WordPress version
   - PHP version
   - Screenshots if applicable

**Feature Request?** Same process, choose "Feature Request" template.

### Code Review Standards

When reviewing pull requests, check for:

- [ ] Code follows WordPress standards
- [ ] All input is sanitized
- [ ] All output is escaped
- [ ] Functions are documented (PHPDoc)
- [ ] No security vulnerabilities
- [ ] Performance impact is minimal
- [ ] Changes don't break existing features
- [ ] Commit messages are clear
- [ ] No commented-out code left behind
- [ ] No debugging code (console.log, var_dump)

### Getting Help

**Documentation:**
- WordPress Codex: https://codex.wordpress.org/
- Plugin Handbook: https://developer.wordpress.org/plugins/

**Questions?**
- Open a GitHub Discussion
- Or email: [your-support-email]

**Security Issues?**
- DO NOT open a public issue
- Email directly: [your-security-email]

---

## Support & Feedback

**Plugin Homepage:** https://github.com/markfenske84/traffic-growth-projection  
**Report Issues:** https://github.com/markfenske84/traffic-growth-projection/issues  
**Feature Requests:** https://github.com/markfenske84/traffic-growth-projection/issues

For general WordPress help, visit the [WordPress Support Forums](https://wordpress.org/support/).

---

## License

This plugin is licensed under the GPL v2 or later.

```
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

---

**Last Updated:** November 4, 2025  
**Documentation Version:** 1.0

