# Race Series Manager – Admin Help

This plugin lets you create **Events** and add multiple **Races** under each event. Use the guidance below to manage content in wp-admin and embed race lists on any page with the bundled shortcodes.

## Creating events
- Add a new **Event** (post type `cmt_event`).
- Fill the content tabs (Announcement, Rules, Schedule, Access, Travel) and the **Event logo & contact** box if you want them to appear in the race booklet PDF.
- In **Registration, participants & live**, paste URLs for online registration, participant lists, live timing, or other off-site tools. These links are reused by the shortcode buttons.
- Publish/update the event so it receives a slug and ID.
- To quickly reuse a setup, hover an event row in **RS Manager → Events** and click **Clone** to create a draft copy with all meta fields duplicated.

## Adding races to an event
- Add a new **Race** (post type `cmt_race`) for each distance.
- In the **Race Details** box, select the parent event, date, start time, distance, elevation, and start location. These values are shown in the shortcode table.
- Publish/update the race. Repeat for additional distances.
- Use the **Clone** row action on a race to duplicate details (including media/meta) into a new draft you can edit.

## Shortcodes
### Event overview
List all races of an event with action buttons for registration, participants, live timing, and results (when configured):

```
[rsm_event_overview event="EVENT_SLUG_OR_ID"]
```

- Replace `EVENT_SLUG_OR_ID` with either the event slug (preferred) or the numeric event ID.
- Place the shortcode in any page/post or in a block’s shortcode block.
- The table is automatically ordered by race date and shows the race title, distance, elevation, date, start time, and start location.
- Buttons appear only when the related event URLs (registration/participants/live) are filled. A **Results** button shows when a results page exists for the event.

### Finding the event slug or ID
- When editing an event, the sidebar **RS Manager Shortcodes** box shows ready-to-copy shortcode examples for that event.
- When editing a race, a similar meta box shows the race link shortcode and, when available, the parent event’s overview shortcode.
- You can also copy the slug from the event permalink or the ID from the URL parameter `post=123` in the admin URL.

### Event and race link shortcodes
Insert a simple link to a specific event or race anywhere:

```
[rsm_event_link event="EVENT_SLUG_OR_ID"]
[rsm_race_link race="RACE_SLUG_OR_ID"]
```

- Optional `label` attribute overrides the linked text. Without it, the event/race title is used.

## Viewing IDs and ordering
- The **ID** column now appears in **Events**, **Races**, and **Results** lists for quick reference.
- An **Order** column is available and sortable; change the number in **Quick Edit** or the **Page Attributes → Order** box on the edit screen to control manual ordering.

## Troubleshooting
- If the shortcode says “Event not found,” confirm the `event` attribute matches an existing event slug/ID and that the event is published.
- If a race row shows dashes (—), add the missing details in the Race Details box and update the race.
- If buttons are missing, verify the corresponding event URLs are set in **Registration, participants & live**.

## Configuration
- Go to **RS Manager → Settings** to adjust the button labels used above the race table, control whether action buttons open in a new tab, and toggle whether the event excerpt shows above the list.
- Save changes to immediately update all pages using the shortcode.
- The settings page also includes a **PDF Generator Status** panel that checks whether the bundled Dompdf library is present and readable for race booklet exports.
