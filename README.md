# ConsentBundle
Provides a mechanism for managing email consent via AWS DynamoDb

## Installation

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
        password: secretpasswordforemailencryption

## Example Usage
        $consentHandler = $this->get('vouchedfor_consent');

        $encryptedEmail = $consentHandler->encrypt('info@test.com');
        
        $services = [
            'marketing_emails': true,
            'service_emails': true,
            'third_party_emails: false
        ];

        $consentHandler->update($encryptedEmail, '2018-01-03 12:30:12', $services);

## License

The Consent Bundle is free to use and is licensed under the [MIT license](http://www.opensource.org/licenses/mit-license.php)

