# Tura Notes — signing in to the application through the site

Three routes let the **Tura Notes** application receive its connection to the cloud
without anyone typing a key: the application opens the site in the browser, the
admin signs in and allows the device, and the application receives that device's
credential. The whole contract (the flow, the reason for each step, the
application's side) is
[`docs/PAIRING.md` in the Tura repository](https://github.com/samirhvbr/tura-notes/blob/master/docs/PAIRING.md);
this file says only what **this** site does and what it takes to switch it on.

## The routes

| Route | Called by | What it does |
|---|---|---|
| `GET /tura/pair` | the browser, opened by the application | The consent screen. Needs the admin logged in (`auth`, `admin`, `password.changed`): anyone who is not goes to the login and **comes back** here. It validates the parameters and refuses anything outside the contract (400), creating nothing. |
| `POST /tura/pair` | the screen's form | **Allow** mints a credential (`tura-credential create`) and keeps it for 120 s under a single-use code; **Deny** only tells the application. It answers with the page that hands control back through `tura://pair?...`. |
| `POST /tura/pair/exchange` | the application, with no session | Trades `{code, verifier}` for `{origin, workspace, credential, label}`. JSON only. **Exempt from CSRF** (the only route that is), limited to 30 a minute per IP. |

## What the code guarantees

- **The secret is never in a URL.** The redirect carries only the code. Another
  application on the device can claim `tura://`; what it carries is useless without the
  `verifier`, which never left the application (PKCE, RFC 7636, S256).
- **The code is good once.** The exchange removes it before checking the `verifier`,
  under a lock (the database cache's `pull` is not atomic). A wrong `verifier` spends it too.
- **Every failure of the exchange is the same:** `400 {"error":"invalid_grant"}`. A spent,
  expired or unknown code and a `verifier` that does not match cannot be told apart.
- **The secret waits encrypted** (the application key), under a cache key that is the
  *hash* of the code. It goes to no log, queue, session or error message; the audit records
  the device's name, never the code nor the secret.
- Every page is `no-store`, `X-Frame-Options: DENY` and `Referrer-Policy: no-referrer`.
  `/tura/*` is not counted as a visit (`TrackPageView`).

## Switching it on

1. **`TURA_ORIGIN`** in `.env`: the public address of the server the application will
   use (default `https://tura.samirhv.com.br`). It is what the exchange returns as `origin`.
2. Nothing new on the server: the `tura-credential` wrapper and the sudoers line that the
   `/admin/tura` screen already uses are enough, and the wrapper already accepts the six
   permissions (`read,create,update,move,delete,search`). A pairing credential carries
   `search`, which the `/admin/tura` screen does not offer, because the Tura remote folder
   uses it; `devices` is left out on purpose.
3. Without the wrapper installed, the consent screen **explains** instead of failing with a
   500 (the same posture as `/admin/tura`).

Every "Allow" creates **one credential per device**, named after what the device called
itself (in the alphabet the wrapper accepts). It shows up in `/admin/tura`, where it is
revoked on its own. Signing in again on the same device does **not** replace the earlier
credential: revoke it.

## Tests

`tests/Feature/TuraPairingTest.php` (43 cases): who sees the screen, each request outside
the contract, what "Allow" mints and with which permissions, that the secret is never in a
page or a log, the single-use exchange, the wrong `verifier`, expiry, a body outside the
contract, the per-IP limit. No database, like the rest of the suite.
