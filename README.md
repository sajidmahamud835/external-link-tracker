# External Link Tracker

**Description:** A WordPress plugin for tracking external link clicks with detailed insights and a user-friendly dashboard. Includes a customizable countdown warning and easy-to-use settings for inserting GTM, advertising, and tracking code.

## Features
- **Click Tracking**: Logs destination, referrer, and timestamp of each external click.
- **Countdown Warning**: Display a custom message and countdown timer before redirecting users.
- **Custom Code Injection**: Easily add GTM, advertising, or other tracking code to the `<head>` and `<body>` of the warning page.
- **Admin Dashboard**: View click data in a modern, organized dashboard with sortable columns for easy data review.

## Installation
1. Download and unzip the `external-link-tracker` folder in `wp-content/plugins/`.
2. Activate the plugin under **Plugins > Installed Plugins**.
3. Customize settings in **Settings > Link Tracker**.

## Usage
1. **Settings Page**: Go to **Settings > Link Tracker** to configure:
   - **Warning Message**: Custom message before users are redirected.
   - **Countdown Time**: Set the countdown timer duration.
   - **Custom HTML for Tracking**: Add GTM, advertising, or other tracking code to `<head>` and `<body>`.
2. **Admin Dashboard**: Access click data in **Link Tracker > Dashboard** for organized insights into outbound link activity.

## Technical Details
- **Database Table**: Stores link click data in a custom table (`wp_elt_link_tracking`).
- **Dashboard Display**: Shows click insights in an easy-to-read table with sortable columns.

## License
Distributed under GPL-2.0 License. See [LICENSE](http://www.gnu.org/licenses/gpl-2.0.html).

## Support
For issues, visit [GitHub Issues](https://github.com/sajidmahamud835/external-link-tracker/issues/new).
