Race Series Manager – WordPress Plugin

Race Series Manager is a custom WordPress plugin designed for managing endurance events such as running races, cycling races, triathlons, and multi-day race series.
It provides an intuitive backend for organizing events and races, embedding registration and results, and displaying all information on the frontend with clean, theme-friendly templates.

✨ Features
🏁 Race & Event Management

Create events and assign multiple races under each event.

Add race details: date, time, distance, elevation, location, type, and custom metadata.

Backend UI with custom meta boxes for managing all race attributes.

📊 Results Integration

Add iframe embed code or external link for results.

Results appear as a dedicated button on race pages and as a side panel on event pages.

📝 Registration Options

Support for iframe registration pages or external registration URLs.

Automatic “Register” button on races and events.

👥 Participants List

Show participant lists using iframe code or external links.

A “Participants” button appears on all relevant frontend pages.

🔴 Live Tracking

Embed live timing / tracking via iframe or URL.

Displayed as a “Live” button for quick access during the event.

🧩 Theme & Template Friendly

Clean output structure designed for easy styling and theme integration.

Works with block themes, classic themes, or custom templates.

📦 Installation

Download or clone the repository into your WordPress installation:

wp-content/plugins/race-series-manager


Activate the plugin from the WordPress Admin → Plugins screen.

Navigate to Events in the admin menu to begin adding events and races.

🛠️ How It Works
Custom Post Types

Events (parent entity)

Races (child entity assigned to an Event)

Meta Boxes

Each race includes:

Race details (distance, date, elevation, type, etc.)

Registration (iframe or external link)

Participants (iframe or link)

Results (iframe or link)

Live page (iframe or link)

Frontend Output

Each race page automatically displays buttons for Register, Participants, Results, and Live, depending on available data.

Event pages show:

All races belonging to that event

Buttons for results / registration / participants per race

A right-hand quick-access section for embeds (similar to results panel)

💡 Use Cases

Perfect for:

Race organizers

Sports event management companies

Running & cycling clubs

Websites hosting multi-stage or multi-race competitions

🔧 Developer Notes

Actions and filters are structured for future extensibility.

Template parts can be overridden by themes if needed.

Clean, modular PHP architecture for easy maintenance.

If you'd like, I can also generate:

A full Hooks & Filters Reference

Code examples for overriding templates

A diagram of the plugin’s architecture

📄 License

This project is open-source under the GPL-2.0+ license.

🤝 Contributing

Pull requests, feature suggestions, and bug reports are welcome!
Feel free to open an issue or fork the repository.
