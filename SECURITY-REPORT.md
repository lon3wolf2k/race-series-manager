# Security Review for Race Series Manager

## Summary
Another audit focused on the remaining attack surface of race embeds and booklet generation. The embed sanitizer now enforces an HTTPS-only, provider allow-list for iframe sources so malicious `javascript:` or untrusted hosts are rejected at save/render time. Dompdf booklet rendering remains nonce-gated with remote fetching disabled. No new critical issues were identified beyond the tightened embed scope.

## Resolved issues

### 1) Stored XSS via race embed fields (fixed)
* **What changed**: Embed HTML for plotaroute/video fields is sanitized through `wp_kses` using a dedicated iframe allow-list before storage and again on output. Inline `style` attributes are disallowed to prevent layout takeovers, and iframe `src` values must now be HTTPS and match a curated host allow-list (Plotaroute, YouTube, Vimeo, Google Maps, OpenStreetMap) to block `javascript:` or untrusted embeds.【F:includes/meta-race.php†L13-L129】【F:templates/single-cmt_race.php†L33-L44】
* **Result**: Authors can no longer inject scripts or arbitrary tags through these fields; only iframes pointing at approved providers with safe attributes are persisted/rendered, eliminating stored-XSS vectors via crafted embed codes.【F:includes/meta-race.php†L533-L599】【F:templates/single-cmt_race.php†L262-L307】

### 2) Unauthenticated PDF generation SSRF/resource abuse (fixed)
* **What changed**: Requests to `?rsm_booklet=1` now require a per-race nonce embedded in the race page link, and Dompdf remote fetching has been disabled.【F:templates/single-cmt_race.php†L26-L37】【F:includes/pdf-booklet.php†L92-L139】
* **Result**: Blind PDF generation attempts are rejected without a valid nonce, and Dompdf will no longer make remote network requests during rendering, closing the SSRF/resource exhaustion vector.【F:includes/pdf-booklet.php†L114-L141】

## Additional observations (current review)
* **Race meta box handling**: Saving meta fields continues to enforce capability checks, autosave guards, and sanitization (URLs via `esc_url_raw`, gallery IDs coerced to integers, embeds sanitized through the allow-list and host checks), limiting injection and privilege-escalation risk in the admin flow.【F:includes/meta-race.php†L548-L599】
* **Front-end rendering**: Race page output escapes URLs and text consistently (e.g., breadcrumbs, stat cards, external buttons, GPX download), and embed HTML passes back through the sanitizer prior to rendering, preventing stored XSS even if database rows were tampered with.【F:templates/single-cmt_race.php†L45-L112】【F:templates/single-cmt_race.php†L262-L528】
* **Residual risk**: Allowing iframe embeds still inherits the provider’s security posture; avoid enabling unknown providers and prefer HTTPS sources. No unmitigated code execution or privilege bypass paths were found in this review.
