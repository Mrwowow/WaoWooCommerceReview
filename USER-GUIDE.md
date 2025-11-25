# WaoWooCommerceReview - Complete User Guide

## Table of Contents
1. [Installation](#installation)
2. [Initial Setup](#initial-setup)
3. [Creating Your First Campaign](#creating-your-first-campaign)
4. [Setting Up Email Reminders](#setting-up-email-reminders)
5. [Customizing Review Display](#customizing-review-display)
6. [Managing Campaigns](#managing-campaigns)
7. [Viewing Analytics](#viewing-analytics)
8. [Advanced Settings](#advanced-settings)
9. [Troubleshooting](#troubleshooting)
10. [Best Practices](#best-practices)

---

## Installation

### Step 1: Check Requirements

Before installing, ensure your WordPress site meets these requirements:
- ✅ WordPress 5.0 or higher
- ✅ WooCommerce 5.0 or higher (must be installed and activated)
- ✅ PHP 7.0 or higher
- ✅ MySQL 5.6 or higher

### Step 2: Install WooCommerce (if not already installed)

1. Go to **Plugins > Add New** in your WordPress dashboard
2. Search for "WooCommerce"
3. Click **Install Now** on the WooCommerce plugin
4. Click **Activate**
5. Complete the WooCommerce setup wizard

### Step 3: Install WaoWooCommerceReview

**Method A: Upload via WordPress Admin (Recommended)**

1. Download the plugin ZIP file from GitHub:
   - Go to https://github.com/Mrwowow/WaoWooCommerceReview
   - Click the green **Code** button
   - Select **Download ZIP**

2. In your WordPress admin panel:
   - Navigate to **Plugins > Add New**
   - Click **Upload Plugin** at the top
   - Click **Choose File** and select the downloaded ZIP file
   - Click **Install Now**
   - Wait for the upload and installation to complete
   - Click **Activate Plugin**

**Method B: Manual Installation via FTP**

1. Download and extract the plugin ZIP file
2. Connect to your server via FTP (using FileZilla, Cyberduck, etc.)
3. Navigate to `/wp-content/plugins/`
4. Upload the entire `WpWooCommerceViewReview` folder
5. Go to **Plugins** in WordPress admin
6. Find **WaoWooCommerceReview** and click **Activate**

### Step 4: Verify Installation

After activation, you should see:
- A success message: "Plugin activated"
- A new menu item **Review Rewards** with a star icon in your WordPress admin sidebar
- No error messages

---

## Initial Setup

### Step 1: Access Plugin Settings

1. In your WordPress admin, look for **Review Rewards** in the left sidebar (with a star icon ⭐)
2. The plugin adds these menu items:
   - **Dashboard** - Overview of your review metrics
   - **Campaigns** - Manage reward campaigns
   - **Email Templates** - Configure review reminder emails
   - **Settings** - Plugin configuration
   - **Analytics** - Detailed performance metrics

### Step 2: Configure Basic Settings

1. Click **Review Rewards > Settings**
2. You'll see three tabs: **General**, **Email Settings**, and **Display**

#### General Settings Tab

Configure what customers can do with reviews:

**Photo Reviews:**
- ✅ Check **Enable Photo Reviews** to allow image uploads
- Recommended: Keep this enabled for better social proof

**Video Reviews:**
- ✅ Check **Enable Video Reviews** to allow video uploads
- Note: Videos can be large; consider your hosting limitations

**Max Uploads Per Review:**
- Set to `5` (default) or adjust based on your needs
- Lower numbers reduce server load
- Higher numbers provide more content

**Review Editing:**
- ✅ Check **Enable Review Editing** to let customers edit their reviews
- Useful for fixing typos or updating opinions

**Helpful Voting:**
- ✅ Check **Enable Helpful Voting** to add "Helpful" buttons to reviews
- Helps highlight quality reviews

**Mask Reviewer Names:**
- ☐ Leave unchecked for full transparency
- ✅ Check to protect customer privacy (shows partial names like "John D.")

**Auto Delete Expired Coupons:**
- ✅ Check to automatically remove expired coupons daily
- Keeps your coupon list clean

3. Click **Save Changes** at the bottom

#### Email Settings Tab

Configure how review reminder emails are sent:

**SendGrid API Key (Optional but Recommended):**
- Leave blank to use standard WordPress email (wp_mail)
- OR enter your SendGrid API key for better deliverability
- **To get a SendGrid API Key:**
  1. Go to https://sendgrid.com and sign up (free plan available)
  2. Verify your email address
  3. Go to Settings > API Keys
  4. Click "Create API Key"
  5. Give it a name like "WooCommerce Review Plugin"
  6. Select "Full Access" or "Restricted Access" with Mail Send permissions
  7. Copy the API key (you'll only see it once!)
  8. Paste it into the SendGrid API Key field

**From Name:**
- Default: Your site name
- Example: "Acme Store" or "Sarah's Boutique"
- This appears as the email sender name

**From Email:**
- Default: Your WordPress admin email
- Use a branded email like: support@yourstore.com
- Must be a valid email address you own

3. Click **Save Changes**

#### Display Tab

Customize how reviews look on your site:

**Primary Color:**
- Click the color picker
- Choose a color that matches your brand
- Used for buttons, links, and accents
- Example: Your brand's main color

**Star Rating Color:**
- Click the color picker
- Default: Orange (#ffa500)
- Used for star ratings and progress bars
- Tip: Yellow or gold works well

3. Click **Save Changes**

---

## Creating Your First Campaign

Campaigns are rules that determine when and how customers receive discount rewards for leaving reviews.

### Example Campaign: "First Review Reward"

Let's create a campaign that gives customers a 10% discount after their first review:

1. **Go to Review Rewards > Campaigns**
2. **Click "Add New Campaign"** (blue button at the top)

A popup window will appear with the campaign form:

### Campaign Settings

**Campaign Name:**
- Enter: `First Review Reward`
- This is for your reference only (customers won't see it)

**Reviews Required:**
- Enter: `1`
- This means the reward triggers after 1 review
- You could set this to 3, 5, or any number for repeat customers

**Coupon Type:**
- Select: **Percentage Discount**
- Options:
  - **Percentage Discount**: 10% off, 20% off, etc.
  - **Fixed Cart Discount**: $5 off, $10 off entire cart
  - **Fixed Product Discount**: $5 off, $10 off specific products

**Coupon Amount:**
- Enter: `10` (for 10% off)
- Or `5` for $5 off if using fixed discount

**Validity Days:**
- Enter: `30`
- Coupon expires 30 days after being issued
- Recommended: 14-60 days to create urgency

**Usage Limit:**
- Enter: `1`
- Customer can use this coupon 1 time
- Set higher for repeat purchases

**Minimum Spend:**
- Enter: `0` (no minimum)
- Or `50` to require $50 minimum purchase
- Helps protect profit margins

**Additional Options:**

☐ **Free Shipping**
- Check this to include free shipping with the discount
- Great incentive for higher-value reviews

☐ **Exclude Sale Items**
- Check to prevent discount on already-discounted products
- Protects your margins

☐ **Individual Use Only**
- Check to prevent stacking with other coupons
- Recommended to control costs

**Status:**
- Select: **Active**
- Set to Inactive to pause the campaign without deleting it

3. **Click "Save Campaign"**

You should see a success message and your new campaign in the list!

### Creating Additional Campaigns

**Example 2: "VIP Reviewer" (After 5 Reviews)**
- Campaign Name: `VIP Reviewer`
- Reviews Required: `5`
- Coupon Type: Fixed Cart Discount
- Coupon Amount: `25`
- Validity Days: `60`
- Free Shipping: ✅ Checked
- Status: Active

**Example 3: "Photo Review Bonus"**
- Create a general campaign
- Monitor which reviews have photos in Analytics
- Manually reward photo reviewers with special codes
- (Automatic photo detection coming in future update)

---

## Setting Up Email Reminders

Automated emails encourage customers to leave reviews after purchase.

### Step 1: Configure Email Template

1. **Go to Review Rewards > Email Templates**

### Understanding Placeholders

You can use these placeholders in your emails - they'll automatically be replaced with real data:

- `{customer_name}` - Customer's first name (e.g., "John")
- `{order_id}` - Order number (e.g., "#1234")
- `{products_list}` - Clickable list of purchased products with review links

### Default Email Template

**Subject Line:**
```
How was your recent purchase?
```

**Alternative Subject Lines:**
- "We'd love your feedback, {customer_name}!"
- "Review your order #{order_id} and get rewarded!"
- "Share your thoughts and save on your next order"

**Trigger:**
- Select: **After Purchase Completion**
- This sends email when order status changes to "Completed"

**Send After (days):**
- Enter: `7`
- Recommended: 3-14 days
- Too soon: Customer may not have received/used product
- Too late: Customer may forget their experience

**Header Color:**
- Click color picker
- Match your brand color
- This colors the email header banner

**Email Content:**

Here's a tested template you can customize:

```html
<p>Hi {customer_name},</p>

<p>Thank you for your recent purchase! We hope you're enjoying your new items.</p>

<p>We would love to hear your feedback on the following products:</p>

{products_list}

<p><strong>🎁 Special Reward:</strong> As a thank you for sharing your review, we'll send you an exclusive discount code to use on your next order!</p>

<p>Your review helps other customers make informed decisions and helps us improve our products and service.</p>

<p>Thank you for being a valued customer!</p>

<p>Best regards,<br>
The Team at [Your Store Name]</p>
```

### Personalization Tips

**For Luxury Brands:**
```html
<p>Dear {customer_name},</p>

<p>We trust your recent purchase meets our exacting standards.</p>

<p>Your discerning feedback would be invaluable:</p>

{products_list}

<p>As appreciation for your time, please accept an exclusive offer upon sharing your thoughts.</p>

<p>With gratitude,<br>
[Your Brand]</p>
```

**For Casual/Fun Brands:**
```html
<p>Hey {customer_name}! 👋</p>

<p>How's your new stuff? We're dying to know!</p>

<p>Drop a quick review here:</p>

{products_list}

<p>🎉 BONUS: Leave a review and we'll hook you up with a sweet discount code for next time!</p>

<p>Thanks for being awesome! 🙌<br>
The [Store Name] Crew</p>
```

2. **Click "Save Template"**

### Step 2: Test Your Email

Before going live, send yourself a test:

1. Enter your email in the **Test Email Address** field
2. Click **Send Test Email**
3. Check your inbox (and spam folder)
4. Verify:
   - ✅ Email arrives
   - ✅ Colors look good
   - ✅ Formatting is correct
   - ✅ Links work (placeholders will show as dummy data in test)

### Step 3: Activate Email Automation

Once saved, emails will automatically send based on your trigger settings. The system:
1. Monitors order status changes
2. Schedules emails based on your "Send After" days
3. Sends emails at the scheduled time
4. Logs all email activity for tracking

---

## Customizing Review Display

Make reviews match your brand and stand out on product pages.

### Step 1: Set Your Brand Colors

1. **Go to Review Rewards > Settings > Display tab**

2. **Primary Color:**
   - This colors:
     - Vote "Helpful" buttons
     - Media upload borders
     - Action buttons
   - Click the color box to open picker
   - Choose your brand's main color
   - Example: Blue (#0073aa), Green (#00a32a), Red (#dc3545)

3. **Star Rating Color:**
   - This colors:
     - Star ratings
     - Progress bars in rating summaries
   - Default: Orange (#ffa500)
   - Popular choices: Gold (#ffd700), Yellow (#ffeb3b)

4. **Click "Save Changes"**

### Step 2: Preview on Frontend

1. Go to any product page on your store
2. Scroll to the Reviews section
3. Check that colors match your brand
4. Test the "Helpful" button
5. Try uploading a test review with image

### Advanced Customization (Optional)

If you're comfortable with CSS, you can add custom styles:

1. Go to **Appearance > Customize > Additional CSS**
2. Add custom CSS to override default styles:

```css
/* Increase star size */
.star-rating {
    font-size: 24px !important;
}

/* Change review card style */
.wao-wcr-review-media {
    border: 2px solid #f0f0f0;
    padding: 15px;
    border-radius: 12px;
}

/* Customize helpful button */
.wao-wcr-vote-helpful {
    background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 10px 20px;
}

/* Make rating bars rounded */
.wao-wcr-progress-bar {
    border-radius: 15px;
}
```

---

## Managing Campaigns

### Viewing All Campaigns

1. **Go to Review Rewards > Campaigns**
2. You'll see a table with all campaigns showing:
   - Campaign Name
   - Review Count Required
   - Coupon Type
   - Amount
   - Status (Active/Inactive)
   - Actions (Edit/Delete)

### Editing a Campaign

1. Click **Edit** next to the campaign name
2. The campaign form opens with current settings
3. Make your changes
4. Click **Save Campaign**

**Common edits:**
- Increase coupon amount for holidays/promotions
- Change validity days
- Pause campaign (set Status to Inactive)
- Update minimum spend requirements

### Pausing a Campaign

Instead of deleting, pause campaigns temporarily:

1. Click **Edit** on the campaign
2. Change **Status** to **Inactive**
3. Click **Save Campaign**

The campaign stops triggering but keeps all historical data.

### Deleting a Campaign

⚠️ **Warning:** This permanently removes the campaign (but doesn't affect already-issued coupons).

1. Click **Delete** next to the campaign
2. Confirm the deletion
3. Campaign is removed

**Best Practice:** Pause campaigns instead of deleting to preserve history.

### Campaign Strategy Tips

**Single Review Reward:**
- Most popular
- Encourages all customers to review
- Example: "10% off after 1 review"

**Tiered Rewards:**
- Reward repeat reviewers
- Campaign 1: 10% off after 1 review
- Campaign 2: 15% off after 5 reviews
- Campaign 3: $25 off + free shipping after 10 reviews

**Product-Specific Campaigns:**
- Create different campaigns for different products
- High-margin products: Higher rewards
- Low-margin products: Smaller rewards
- New products: Extra incentive for early reviews

**Seasonal Campaigns:**
- Holiday Campaign: "20% Holiday Reward for Reviews"
- Back to School: "Review & Save for Fall"
- Black Friday: "BFCM Review Bonus - 25% Off"

---

## Viewing Analytics

Track your review performance and campaign effectiveness.

### Dashboard Overview

1. **Go to Review Rewards > Dashboard**

You'll see four key metric cards:

**📊 Total Reviews**
- All approved reviews across all products
- Shows growth over time

**📸 Reviews with Media**
- Reviews that include photos or videos
- Higher engagement indicator

**🎁 Coupons Generated**
- Total reward coupons issued
- Tracks campaign effectiveness

**⭐ Average Rating**
- Overall store review rating
- Important for SEO and trust

### Rating Distribution

See how your reviews break down by star rating:

- 5⭐ - Excellent reviews
- 4⭐ - Good reviews
- 3⭐ - Average reviews
- 2⭐ - Below average reviews
- 1⭐ - Poor reviews

**Progress bars** show the percentage of each rating.

**What to track:**
- High 5-star percentage = Happy customers
- Many 1-2 star reviews = Address quality issues
- Mostly 3-star reviews = Room for improvement

### Recent Reviews

Shows your latest 5 reviews with:
- Customer name
- Star rating
- Product name
- Review excerpt
- Time posted

Click through to moderate or respond to reviews.

### Detailed Analytics

1. **Go to Review Rewards > Analytics**

#### Review Performance Metrics:

**Total Reviews:**
- Cumulative count of all reviews

**Average Rating:**
- Overall store rating (out of 5.0)
- Impact on SEO and conversions

**Reviews with Media:**
- Count of reviews with photos/videos
- Percentage of total reviews

**Total Media Files:**
- All uploaded images and videos

#### Reward Metrics:

**Coupons Generated:**
- Total reward coupons created
- Tracks campaign activity

**Conversion Rate:**
- Percentage of reviews that triggered rewards
- Formula: (Coupons / Reviews) × 100

**Media Upload Rate:**
- Percentage of reviews with media
- Shows engagement level

### Using Analytics for Decisions

**Low Total Reviews?**
- Increase coupon value
- Add free shipping to rewards
- Shorten email delay (send sooner)
- Follow up with second reminder email

**Low Media Upload Rate?**
- Create specific photo review campaign
- Offer bonus for photo reviews
- Make upload process easier
- Show examples of good photo reviews

**Low Conversion Rate?**
- Verify campaigns are Active
- Check review count requirements aren't too high
- Ensure coupons are attractive enough
- Review campaign targeting settings

**High Average Rating?**
- Feature reviews prominently on homepage
- Use reviews in marketing materials
- Request testimonials from 5-star reviewers
- Create social media content from reviews

---

## Advanced Settings

### File Upload Limits

**Adjusting Max Uploads:**
1. Go to **Settings > General**
2. Change **Max Uploads Per Review**
3. Consider:
   - More uploads = more content but slower loading
   - Fewer uploads = faster performance
   - Recommended: 3-5 files

**PHP Upload Limits:**
If customers can't upload files, check PHP settings:
1. Contact your host or access cPanel
2. Check/increase these values in php.ini:
   ```
   upload_max_filesize = 20M
   post_max_size = 25M
   max_file_uploads = 10
   ```

### SendGrid Advanced Configuration

**Tracking Opens and Clicks:**
1. In SendGrid dashboard, go to Settings > Tracking
2. Enable Click Tracking and Open Tracking
3. See which customers engage with emails

**Custom Templates:**
1. Create templates in SendGrid dashboard
2. Use dynamic template ID in plugin
3. Advanced users only

### Scheduled Tasks (Cron Jobs)

The plugin uses WordPress Cron for:
- **Daily:** Delete expired coupons
- **On Schedule:** Send review reminder emails

**If emails aren't sending:**
1. Install "WP Crontrol" plugin
2. Check if `wao_wcr_send_review_reminder` events exist
3. Run events manually to test
4. Consider real cron job (ask your host)

### Performance Optimization

**For High-Traffic Sites:**

1. **Cache Exclusions:**
   - Exclude `/wp-admin/admin-ajax.php` from caching
   - Prevents issues with AJAX voting

2. **Image Optimization:**
   - Use plugin like "Smush" or "ShortPixel"
   - Automatically compresses uploaded review images
   - Reduces loading time

3. **CDN Usage:**
   - Use Cloudflare or similar
   - Serves review images faster globally

---

## Troubleshooting

### Common Issues and Solutions

#### Issue: WooCommerce Required Error

**Symptom:** Plugin won't activate, shows error message

**Solution:**
1. Install and activate WooCommerce first
2. Then activate WaoWooCommerceReview
3. Order matters!

#### Issue: Coupons Not Generating

**Symptom:** Customers leave reviews but don't receive coupons

**Check:**
1. **Campaign Status:** Go to Campaigns, ensure status is "Active"
2. **Review Count:** Verify customer has enough approved reviews
3. **Review Approval:** Check if review is approved (not pending)
4. **Target Products:** If campaign targets specific products, verify match
5. **User Account:** Customer must be logged in and have an account

**Test:**
1. Create test order as customer
2. Complete order
3. Leave review on product
4. Approve review in WordPress admin
5. Check if coupon appears in WooCommerce > Coupons

#### Issue: Emails Not Sending

**Symptom:** Review reminder emails never arrive

**Check:**
1. **Email Settings:** Verify "From Email" is valid
2. **SendGrid API:** If using SendGrid, verify API key is correct
3. **WordPress Mail:** Test WordPress email with a plugin like "Check Email"
4. **Spam Folder:** Check customer's spam/junk folder
5. **Email Logs:** Check Review Rewards > Analytics for send status

**Solutions:**
1. Set up SendGrid (most reliable)
2. Use SMTP plugin like "WP Mail SMTP"
3. Contact your host about email deliverability
4. Verify SPF/DKIM records for your domain

#### Issue: Images Not Uploading

**Symptom:** Customers can't upload photos with reviews

**Check:**
1. **Settings:** Photo reviews enabled in Settings > General
2. **File Size:** Image isn't too large (check PHP limits)
3. **File Type:** Only images/videos allowed (.jpg, .png, .mp4, etc.)
4. **Permissions:** WordPress uploads folder is writable

**Test:**
1. Try uploading in WordPress Media Library
2. If that works, issue is plugin-specific
3. If that fails, issue is server permissions

**Solution:**
1. Contact host to check file permissions on `/wp-content/uploads/`
2. Increase PHP upload limits (see Advanced Settings)
3. Try smaller image files first

#### Issue: Plugin Conflicts

**Symptom:** Site breaks or features stop working after plugin activation

**Common Conflicts:**
- Other review plugins
- Cache plugins (aggressive caching)
- Security plugins (blocking AJAX)

**Solution:**
1. Deactivate all other plugins
2. Activate them one by one
3. Identify which causes conflict
4. Either:
   - Remove conflicting plugin
   - Configure it to allow this plugin
   - Contact support for both plugins

#### Issue: Styles Look Wrong

**Symptom:** Reviews display incorrectly or look broken

**Check:**
1. **Theme Compatibility:** Some themes override plugin styles
2. **Cache:** Clear all caches (browser, plugin, CDN)
3. **CSS Conflicts:** Check browser console for errors

**Solution:**
1. Clear cache
2. Try different theme temporarily to test
3. Add custom CSS to fix (see Customizing Review Display)
4. Contact theme developer

#### Issue: Database Tables Not Created

**Symptom:** Features don't work, database errors appear

**Solution:**
1. Deactivate plugin
2. Reactivate plugin (triggers table creation)
3. If still failing:
   - Check database user permissions
   - Contact hosting support
   - Check error logs in cPanel

#### Issue: Helpful Button Not Working

**Symptom:** Clicking "Helpful" does nothing

**Check:**
1. **JavaScript Errors:** Open browser console (F12), check for errors
2. **jQuery:** Ensure theme loads jQuery
3. **Caching:** Clear cache
4. **User Login:** User may need to be logged in

**Solution:**
1. Try different browser
2. Disable conflicting plugins
3. Verify jQuery loads in theme

---

## Best Practices

### Campaign Management

**Start Conservative:**
- Begin with modest rewards (10% off)
- Monitor redemption rates
- Increase if needed to boost participation

**Use Expiry Dates:**
- 30-day validity creates urgency
- Too short: Customers miss opportunity
- Too long: No urgency to use coupon

**Limit Usage:**
- Set usage limit to 1 per customer
- Prevents abuse
- Encourages new purchases

**Test Everything:**
- Make test purchases yourself
- Complete full customer journey
- Verify coupons arrive and work
- Fix issues before going live

### Email Strategy

**Timing Matters:**
- Ship time + 3-7 days works best
- Too soon: Product not received yet
- Too late: Experience forgotten

**Keep It Short:**
- Busy customers skim emails
- Clear call-to-action
- Mention reward prominently
- 2-3 paragraphs maximum

**Mobile Optimization:**
- Most emails opened on mobile
- Test on phone
- Keep subject line under 50 characters
- Use large, tappable buttons

**Don't Over-Send:**
- One reminder per order is enough
- Multiple emails feel spammy
- Respect customer preferences

### Review Management

**Respond to Reviews:**
- Thank customers for positive reviews
- Address concerns in negative reviews
- Show you care about feedback
- Builds trust with future customers

**Moderate Promptly:**
- Review and approve daily
- Respond within 24-48 hours
- Deal with fake reviews quickly

**Feature Great Reviews:**
- Screenshot excellent reviews
- Share on social media
- Use in email marketing
- Add to homepage

**Learn from Negative Reviews:**
- Common complaints = product issues
- Address root causes
- Improve products/service
- Follow up with unhappy customers

### Analytics Usage

**Check Weekly:**
- Monitor review trends
- Track campaign performance
- Adjust strategies
- Identify issues early

**Set Goals:**
- Target: 20%+ of customers review
- Target: 4.5+ star average
- Target: 30%+ reviews with photos
- Adjust based on your industry

**A/B Test:**
- Try different email subject lines
- Test coupon amounts (10% vs 15%)
- Compare timing (3 days vs 7 days)
- Use data to optimize

### Legal Compliance

**Disclosure:**
- Make reward policy clear
- Don't incentivize positive reviews only
- Offer same reward for any honest review
- Follow FTC guidelines

**Authenticity:**
- Only reward genuine customers
- Don't incentivize fake reviews
- Remove suspicious reviews
- Maintain integrity

**Privacy:**
- Protect customer data
- Secure email communications
- Honor opt-out requests
- Follow GDPR if applicable

### Performance Tips

**Regular Maintenance:**
- Clean expired coupons monthly
- Archive old analytics data
- Update plugin regularly
- Monitor server resources

**Backup:**
- Backup database before updates
- Save campaign settings
- Export data periodically
- Use backup plugin like UpdraftPlus

**Monitor Server Load:**
- Watch for slow page loads
- Optimize images
- Use caching
- Consider CDN for media

---

## Quick Reference

### Common Tasks Checklist

**Weekly:**
- [ ] Check new reviews
- [ ] Respond to reviews
- [ ] Monitor coupon usage
- [ ] Review analytics

**Monthly:**
- [ ] Evaluate campaign performance
- [ ] Adjust reward amounts if needed
- [ ] Clean expired coupons
- [ ] Update email templates seasonally

**Quarterly:**
- [ ] Analyze trends
- [ ] Test new campaign ideas
- [ ] Review customer feedback
- [ ] Optimize based on data

### Support Resources

**Plugin Documentation:**
- GitHub: https://github.com/Mrwowow/WaoWooCommerceReview
- Issues: https://github.com/Mrwowow/WaoWooCommerceReview/issues

**WordPress Resources:**
- WordPress Codex: https://codex.wordpress.org/
- WooCommerce Docs: https://woocommerce.com/documentation/

**SendGrid Help:**
- SendGrid Docs: https://docs.sendgrid.com/
- API Key Setup: https://docs.sendgrid.com/ui/account-and-settings/api-keys

---

## Conclusion

WaoWooCommerceReview transforms customer reviews into a powerful marketing and sales tool. By rewarding customers for their feedback, you:

✅ Increase review quantity and quality
✅ Build social proof and trust
✅ Encourage repeat purchases
✅ Gather valuable customer insights
✅ Boost SEO with fresh content
✅ Improve customer loyalty

Follow this guide step-by-step, and you'll have a fully automated review rewards system driving growth for your WooCommerce store!

**Need Help?** Open an issue on GitHub or consult the troubleshooting section.

**Happy Reviewing! 🌟**
