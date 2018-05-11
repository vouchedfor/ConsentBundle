# ConsentBundle
Provides a mechanism for managing email consent via AWS DynamoDb

## Installation

As this is a private repository, add the following to `composer.json`:

    "repositories": [
        {
            "type": "vcs",
            "url":  "https://github.com/vouchedfor/ConsentBundle.git"
        }
    ],

Install it with composer:

    composer require vouchedfor/consent-bundle:dev-master

Then, add the following in your **AppKernel** bundles:

    // app/AppKernel.php
    public function registerBundles()
    {
        $bundles = array(
            ...
            new VouchedFor\ConsentBundle\VouchedForConsentBundle(),
            ...
        );
        ...
    }

Add the name of the consent table in DynamoDB to `config.yml`. For example:

    // app/config/config.yml
    vouched_for_consent:
        table_name: consent

## Example Usage
        $consentHandler = $this->get('vouchedfor_consent');
        
        $services = [
            'vouchedfor_marketing': true,
            'hatch_marketing': true,
            'vouchedfor_emails: false
        ];

        $consentHandler->update('myemail@test.com', $services);

## License

The Consent Bundle is free to use and is licensed under the [MIT license](http://www.opensource.org/licenses/mit-license.php)

