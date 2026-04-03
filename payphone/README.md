# Payphone Payment Gateway Plugin for Botble CMS

This plugin integrates Payphone payment gateway into your Botble CMS e-commerce platform.

## Features

- Accept payments via Payphone's "Cajita de Pagos" (Payment Box)
- Support for credit/debit cards (Visa, MasterCard, Diners Club, Discover)
- Support for Payphone balance payments
- Secure transaction confirmation via Payphone API
- Online refund support
- Multi-currency support (USD primary)

## Requirements

- Botble CMS 7.3.0 or higher
- PHP 8.0+
- Active Payphone Business account
- SSL certificate (for production)

## Installation

1. Copy the `payphone` folder to `platform/plugins/` directory
2. Go to Admin Panel → Plugins and activate "Payphone Payment Gateway"
3. Navigate to Settings → Payment → Payphone
4. Configure your credentials:
   - **Token**: Your Payphone Bearer Token
   - **Store ID**: Your Store ID from Payphone Developer Dashboard

## Configuration

### Getting Credentials

1. Log in to your Payphone Business account
2. Go to Developer section
3. Create a new WEB application
4. Configure your domain and response URL
5. Copy the Token and Store ID

### Setting up the Payment Box

The plugin automatically includes the Payphone Payment Box SDK. When customers select Payphone as payment method, they will see the payment box embedded in your checkout page.

### Webhook/Callback URL

Configure your callback URL in Payphone dashboard:
```
https://yourdomain.com/payment/payphone/callback
```

## How It Works

1. Customer selects Payphone at checkout
2. Payphone Payment Box appears with configured amount
3. Customer completes payment via card or Payphone balance
4. Payphone redirects to your success/cancel URL
5. Plugin confirms transaction with Payphone API
6. Order status is updated accordingly

**Important**: Transactions must be confirmed within 5 minutes, otherwise Payphone will automatically reverse them.

## Supported Currencies

- USD (primary)

## Transaction Flow

```
Checkout → Payphone Box → Payment Processing → Callback → Confirmation → Complete
```

## Troubleshooting

### Payment not confirming
- Verify your Token and Store ID are correct
- Check that your domain is registered in Payphone dashboard
- Ensure SSL certificate is valid

### Currency errors
- Payphone primarily supports USD
- Other currencies will be converted to USD automatically

## Support

For technical support, contact Payphone developer support or visit https://docs.payphone.app

## License

This plugin is proprietary and requires a valid Botble CMS license.
