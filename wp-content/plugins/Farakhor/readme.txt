# Farakhor Calendar Events

A WordPress plugin to display Persian calendar events with beautiful card designs.

## Description

Farakhor Calendar Events is a WordPress plugin that displays events from the `wp_farakhor` table in a responsive card layout. The plugin supports:

- RTL layout optimized for Persian/Farsi content
- Jalali (Shamsi) calendar integration
- Filtering events by month, category, and tag
- Highlighting current day events
- Responsive design that works on both desktop and mobile devices
- Remaining/passed days calculation
- Persian digit conversion

## Installation

1. Upload the `farakhor-calendar-events` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure your database has a `wp_farakhor` table with the required structure

## Usage

To display the events on any page or post, use the shortcode:

```
[farakhor_events]
```

### Shortcode Parameters

You can customize the display using these optional parameters:

- `limit`: Maximum number of events to display (default: no limit)
- `month`: Filter events by month number (1-12, default: current month)
- `category`: Filter events by category
- `tag`: Filter events by tag

### Examples

Display all events for the current month:
```
[farakhor_events]
```

Display up to 5 events for month 7 (Mehr):
```
[farakhor_events limit="5" month="7"]
```

Display all national events:
```
[farakhor_events category="ملی"]
```

## Database Structure

The plugin expects the following table structure:

```sql
CREATE TABLE wp_farakhor (
  id int AUTO_INCREMENT,
  persian_day varchar(5),
  gregorian_day date,
  event_title text,
  event_text text,
  event_link varchar(255) NULL,
  post_link varchar(255) NULL,
  categories text NULL,
  tag text NULL,
  image varchar(255) NULL,
  -- other fields...
  PRIMARY KEY (id)
);
```

### Key Fields Used by the Plugin

- `persian_day`: In format "M-D", for example "12-25" for 25th of Esfand
- `gregorian_day`: The date in Gregorian calendar
- `event_title`: Title of the event
- `event_text`: Description of the event
- `post_link`: URL for the "اطلاعات بیشتر" (More Information) button
- `categories`: Comma-separated list of categories
- `tag`: Event type or tag
- `image`: URL to the event image

## Styling

The plugin includes built-in styles that match the provided design. If you want to customize the appearance, you can add custom CSS to your theme.

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher

## License

GPL v2 or later