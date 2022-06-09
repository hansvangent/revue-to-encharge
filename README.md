# Revue to Encharge
Add new Revue subscribers (native Twitter newsletter subscription) to your Encharge Newsletter account

## Convert your Twitter followers to newsletter subscribers

Revue to Encharge syncs your Twitter newsletter subscribers to your existing Encharge account.

Your followers can easily subscribe on Twitter, and you can keep using the tools you love.

Wait what?

**Twitters new Revue Badge**
Twitter recently acquired a newsletter organization called Revue. Twitter has now made it possible for you to add a badge to your Twitter profile, where people can easily subscribe to your Revue newsletter. This is awesome and might be a gamechanger for a lot of writers! But what if you want this badge, without having to switch to Revue?

Use the Revue badge to grow your newsletter, without switching to Revue.

This script makes it possible for you to get new subscribers to your existing newsletter on Encharge, without having to switch to Revue.

It will collect your new subscribers in Revue and add them to your existing newsletter Encharge Newsletter list.

When set up using cron, it will keep an eye out for new subscribers on your Revue list and will automatically add them to your existing newsletter list.

Set up once, grow your newsletter forever!

It is super easy to set up! It will only take 5 minutes and will enable you to grow your newsletter via your Twitter profile forever!

Oh and it also check anyone that unsubscribes from your Encharge list and unsubscribes them also from your Revue account to make sure both are always in sync.


## 4 easy steps to get started
### Step 1 Create a Revue account
Create a Revue account, [enable the badge](http://help.getrevue.co/en/articles/5356115-how-to-show-your-newsletter-on-your-twitter-profile) on your Twitter profile and [get your API key](https://www.getrevue.co/app/integrations) to set up this script.

Add your API key in the revue-to-encharge.php by replacing replace_me_with_your_revue_api_key on line 20 with your own key:

```php
$revue_api_key = 'replace_me_with_your_revue_api_key';
```

### Step 2 Get you Encharge API key and Email Category ID's
Next up you need to the the [API key from your Encharge](https://app.encharge.io/account/info) account

Add your API key in the revue-to-encharge.php by replacing replace_me_with_your_encharge_api_key on line 21 with your own key:

```php
$encharge_api_key = 'replace_me_with_your_encharge_api_key';
```

By default Encharge has two email categories for which people can opt-in. Marketing Emails and Transaction Emails. As these categories have different API ID's for everyone you need to find your ID's in the Custom Fields > CommunicationCategories under your account, or [follow this direct link](https://app.encharge.io/settings/person-fields?personfields-folder-item=CommunicationCategories). When you click on the "API Name" field you will see a value that looks something like this "Field name for API  : CommunicationCategories.cat_XXXXX" the numbers at the end are the number you need to fill in with the below variables.

```php
$encharge_marketing_emails_category_id = 'replace_me_with_your_marketing_email_category_id';
$encharge_transactional_emails_category_id = 'replace_me_with_your_transactional_email_category_id';
```

If you added more than the two default email categories, you can add extra lines after line 140 in the script with the number fully in there like this (where the XXXXX is the number of the API field name for those categories):

```php
'CommunicationCategories.cat_XXXXXX' => 'Opted in',
```

#### Extra information
As I'd like to know where my subscribers originally are coming there are two extra lines in the script that can be deleted if you don't want to save this information with your subscribers. Line 137 and 138:

```php
'SOURCE' => 'Twitter Subscriber via Revue',
'tags' => 'Twitter/Revue Subscriber',
```

Will automatically add a new custom field called "SOURCE" that stores where this subscriber came from and it will add the tag "Twitter/Revue Subscriber" for future reference. If you don't want to have this information in your user account you can safely delete these two lines.

### Step 3 Set up a cron job to run the script

Since this will be a script that is designed to run from the command line (although you can let it run from a webserver and invoke a regular check like that too), you will need to [set up a cron job](https://askubuntu.com/questions/2368/how-do-i-set-up-a-cron-job).

For example to run the check every 15 minutes:

``` bash
*/15 * * * * /usr/bin/php /path/to/revue-to-encharge.php
```

Not sure if /usr/bin/php is the correct path to your PHP binary?

Start with finding out your PHP binary by typing in command line:

``` bash
whereis php
```

The output should be something like:

``` bash
php: /usr/bin/php /etc/php.ini /etc/php.d /usr/lib64/php /usr/include/php /usr/share/php /usr/share/man/man1/php.1.gz
```

Specify correctly the full path in your cron-job.

### Step 4 Launch & Grow
That's all. From now on, you can use your Twitter profile to boost the growth of your newsletter. Let's grow!

## Want to grow not just your email list but also your Twitter followers?
You can do this by automatically sharing your best Evergreen Content with your Twitter followers.

This way you can build a following and attract more subscribers to your newsletter.

Simply install the [Evergreen Content Poster](https://www.evergreencontentposter.io/) on your WordPress website. It will help you to easily double your traffic from social media by keeping your content alive and in front of your target audience.

The **Evergreen Content Poster is a unique social media scheduler that does the sharing for you**, by automatically pulling posts from your content library and sharing it to your social media channels.

So you can keep your social media alive every day, on repeat.

## Get Help

- Reach out on [Twitter](https://twitter.com/jcvangent)
- Open an [issue on GitHub](https://github.com/hansvangent/revue-to-encharge/issues/new)

## Contribute

#### Issues

In the case of a bug report, bugfix or a suggestions, please feel very free to open an issue.

#### Pull request

Pull requests are always welcome, and I'll do my best to do reviews as fast as I can.
