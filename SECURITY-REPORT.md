# Security Review for Race Series Manager

## Summary
Follow-up review after implementing sanitization and nonce protections shows the high-risk paths have been mitigated. Race embed fields are now sanitized with a restrictive iframe allow-list both on save and output, and PDF booklet generation requires a nonce and disallows remote fetches in Dompdf.

## Resolved issues

### 1) Stored XSS via race embed fields (fixed)
* **What changed**: Embed HTML for plotaroute/video fields is sanitized through `wp_kses` using a dedicated iframe allow-list before storage and again on output.【F:includes/meta-race.php†L13-L46】【F:templates/single-cmt_race.php†L33-L44】
* **Result**: Authors can no longer inject scripts or arbitrary tags through these fields; only iframe markup with safe attributes is persisted/rendered.【F:includes/meta-race.php†L533-L567】【F:templates/single-cmt_race.php†L274-L285】

### 2) Unauthenticated PDF generation SSRF/resource abuse (fixed)
* **What changed**: Requests to `?rsm_booklet=1` now require a per-race nonce embedded in the race page link, and Dompdf remote fetching has been disabled.【F:templates/single-cmt_race.php†L26-L37】【F:includes/pdf-booklet.php†L92-L139】
* **Result**: Blind PDF generation attempts are rejected without a valid nonce, and Dompdf will no longer make remote network requests during rendering, closing the SSRF/resource exhaustion vector.【F:includes/pdf-booklet.php†L114-L141】
