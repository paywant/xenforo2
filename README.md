# paywant/xenforo2

To use this library, you must have an approved Paywant merchant account and store. You can access Paywant at [https://www.paywant.com](https://www.paywant.com).

[You can click here to download xenforo payment module](https://github.com/paywant/xenforo2/releases/latest)

### Install

- From the Xenforo admin panel, access the Plugins page.
- Click on the Install/Upgrade from Archive button at the top right. Install the Paywant module you downloaded from the popup that opens.

### Activation
- Access the Payment Profiles page under the Setup menu.
- Click the "Add payment profile" button on the top right.
- Select the "Paywant" option from the popup screen and fill in the required fields.

### Callback URL

Your callback url must be;

```bash
https://www.YOUSITEADDRESS.com/payment_callback.php?_xfProvider=paywant
```