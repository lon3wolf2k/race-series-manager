# Race Series Manager – Admin Help

This plugin lets you create **Events** and add multiple **Races** under each event. Use the guidance below to manage content in wp-admin and embed race lists on any page with the bundled shortcode.

## Creating events
- Add a new **Event** (post type `cmt_event`).
- Fill the content tabs (Announcement, Rules, Schedule, Access, Travel) and the **Event logo & contact** box if you want them to appear in the race booklet PDF.
- In **Registration, participants & live**, paste URLs for online registration, participant lists, live timing, or other off-site tools. These links are reused by the shortcode buttons.
- Publish/update the event so it receives a slug and ID.
- To quickly reuse a setup, hover an event row in **RS Manager → Events** and click **Clone** to create a draft copy with all meta fields duplicated.

## Adding races to an event
- Add a new **Race** (post type `cmt_race`) for each distance.
- In the **Race Details** box, select the parent event, date, start time, distance, elevation, start location, and entry fee. These values are shown in the shortcode table.
- Publish/update the race. Repeat for additional distances.
- Use the **Clone** row action on a race to duplicate details (including media/meta) into a new draft you can edit.

## Shortcode: Event overview
The plugin provides a single shortcode to list all races of an event with action buttons for registration, participants, live timing, and results (when configured):

```
[rsm_event_overview event="EVENT_SLUG_OR_ID"]
```

- Replace `EVENT_SLUG_OR_ID` with either the event slug (preferred) or the numeric event ID.
- Place the shortcode in any page/post or in a block’s shortcode block.
- The table is automatically ordered by race date and shows the race title, distance, elevation, date, start time, start location, and entry fee.
- Buttons appear only when the related event URLs (registration/participants/live) are filled. A **Results** button shows when a results page exists for the event.

### Finding the event slug or ID
- When editing an event, the sidebar **Shortcode** meta box shows ready-to-copy shortcode examples for both slug and ID.
- When editing a race, a similar meta box points to its parent event’s shortcode.
- You can also copy the slug from the event permalink or the ID from the URL parameter `post=123` in the admin URL.

## Troubleshooting
- If the shortcode says “Event not found,” confirm the `event` attribute matches an existing event slug/ID and that the event is published.
- If a race row shows dashes (—), add the missing details in the Race Details box and update the race.
- If buttons are missing, verify the corresponding event URLs are set in **Registration, participants & live**.

## Configuration
- Go to **RS Manager → Settings** to adjust the button labels used above the race table, control whether action buttons open in a new tab, and toggle whether the event excerpt shows above the list.
- Save changes to immediately update all pages using the shortcode.
- The settings page also includes a **PDF Generator Status** panel that checks whether the bundled Dompdf library is present and readable for race booklet exports.
