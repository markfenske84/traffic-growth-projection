=== Traffic Growth Projection ===
Contributors: webforagency
Tags: seo, traffic, keywords, analytics, roi
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A comprehensive tool for projecting keyword-based traffic growth, ROI calculations, and conversion tracking.

== Description ==

Traffic Growth Projection is a powerful WordPress plugin that helps you analyze and project traffic growth based on keyword rankings. Perfect for SEO professionals, agencies, and website owners who want to forecast traffic potential and calculate ROI.

= Features =

* **Project Management**: Create multiple traffic projection projects with customizable conversion rates and CLTV
* **CSV Import**: Import keyword data with search volume, difficulty, rankings, and categories
* **Traffic Projections**: Calculate and visualize traffic growth over 12 months
* **Multiple Projection Views**:
  - Current Trajectory (based on existing rankings)
  - Existing Keywords projections
  - Must-Have Keywords projections
  - New Keywords projections
* **Interactive Charts**: Toggle different projection views
* **ROI Calculator**: Calculate conversions, revenue, and ROI based on traffic projections
* **Keyword Management**: View and filter keywords by category
* **Export Reports**: Download projection data as CSV

= CSV Import Format =

Your CSV file should have the following columns:

* **Column A**: Keyword (text)
* **Column B**: Search Volume (number)
* **Column C**: Difficulty (percentage, 0-100)
* **Column D**: Estimated Traffic (number)
* **Column E**: Current Ranking (0-100, optional)
* **Column F**: Expected Ranking (1-10)
* **Column G**: Category (dropdown value)

= Valid Categories =

* Select
* Existing Keywords (transactional terms only)
* Must-Have Keywords (limit to 50)
* New Keywords (limit to 100)

= Ranking to Traffic Capture Rates =

* Rank 1 = 60% of estimated traffic
* Rank 2 = 50% of estimated traffic
* Rank 3 = 40% of estimated traffic
* Rank 4 = 30% of estimated traffic
* Rank 5 = 25% of estimated traffic
* Rank 6 = 20% of estimated traffic
* Rank 7 = 15% of estimated traffic
* Rank 8 = 12% of estimated traffic
* Rank 9 = 8% of estimated traffic
* Rank 10 = 5% of estimated traffic

== Installation ==

1. Upload the `traffic-growth-projection` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to 'Traffic Growth' in the admin menu to get started

== Frequently Asked Questions ==

= How do I import keywords? =

Click "Import Keywords" in your project view and upload a CSV file with the required format. A sample CSV is included in the plugin folder.

= Can I export my projections? =

Yes, click the "Export Report" button to download your projections as a CSV file.

= How are traffic projections calculated? =

Traffic is calculated based on the ranking position and estimated traffic for each keyword, using industry-standard capture rates for each position (1-10).

== Screenshots ==

1. Projects dashboard
2. Traffic growth projection chart
3. ROI calculator
4. Keywords table view
5. CSV import interface

== Changelog ==

= 1.0.2 =
* Improved shortcode display on projects page
* Removed redundant shortcode display from project detail page
* UI refinements

= 1.0.1 =
* Added drag-and-drop project reordering
* Added client-facing frontend view with shortcode support
* Added Gutenberg block for project display
* Added CSV import template download
* Improved WordPress coding standards compliance
* Database upgrade routine for existing installations

= 1.0.0 =
* Initial release
* Project management
* CSV import
* Traffic projections with interactive charts
* ROI calculator
* Keyword management
* Export functionality

== Upgrade Notice ==

= 1.0.2 =
Minor UI improvements. Shortcode display now only on projects page.

= 1.0.1 =
New features: drag-and-drop reordering, client-facing views, and Gutenberg block support.

= 1.0.0 =
Initial release of Traffic Growth Projection plugin.

== Support ==

For support, please visit the plugin support forum.

