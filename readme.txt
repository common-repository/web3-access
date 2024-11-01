=== Web3 Access ===
Contributors: RogueWebDesign
Donate Link: https://metapress.ca/donate
Tags: web3, cryptocurrency, NFT, restrict content, crypto payments
Requires at least: 4.0
Tested up to: 6.6.1
Stable Tag: 1.7.0
License: GPLv2 or later
Accept cryptocurrency payments via MetaMask or web3 browser wallets. Restrict content to NFT owners or crypto wallets that make a payment.

== Description ==

<p>Web3 Access is a Web3 wallet plugin for WordPress that allows you to accept cryptocurrency payments via MetaMask or other browser wallets for access to content on your WordPress website. Restrict specific content or entire Pages, Posts and custom content types to NFT owners, specific cryptocurrency holders or visitors that make a transaction via a web3 browser wallet.</p>

<p>See more Reviews <a href="https://metapress.ca/reviews/" target="_blank">here</a>.</p>

<h3>Features</h3>
<ul>
    <li>Restrict specific content including text, images, videos and more using the <strong>Web3 Access Restricted Content Gutenberg Block</strong>.</li>
    <li>Restrict access to entire Pages, Posts and other post types.</li>
    <li>Accept payments in Ethereum (ETH), Polygon (MATIC), Binance Smart Chain (BSC), Avalanche (AVAX), Fantom (FTM) and Solana (SOL) access to content.</li>
    <li>Add custom ERC-20 tokens of your choice to accept crypto payments via browser wallets, allowing visitors to access your content.</li>
    <li>Set prices in USD. Web3 Access automatically converts the price to the amount in tokens at the time of transaction. (limitations may apply for certain tokens)</li>
    <li>Allow access to content via NFT ownership verification. <a href="https://metapress.ca/nft-ownership-verification/" target="_blank">See Details</a></li>
    <li><strong>*NEW</strong> Create subscriptions for limited time access to your content. Visitors can renew subscriptions with a Web3 payment or NFT Verification.</li>
</ul>

<h3>Supported Networks</h3>
<p>The Web3 Access plugin currently supports the following networks:</p>
<ul>
<li>Ethereum Mainnet</li>
<li>Polygon (MATIC)</li>
<li>Binance Smart Chain (BSC)</li>
<li>Avalanche (AVAX)</li>
<li>Fantom (FTM)</li>
<li>Solana (SOL) via Phantom Wallet - transaction payments support only. NFT verification under development for Solana.</li>
</ul>

<h3>Transaction Fee</h3>
<p>We collected a 1% transaction fee for payments made on our supported Networks.</p>
<p>Our NFT Verification feature is free to use and can verify ownership for any tokens that exist on the networks above. ERC-1155 tokens have limitations, but can be verified via an OpenSea API Key.</p>

== Installation ==

1. Upload the web3-access folder to the `/wp-content/plugins` directory, or install the plugin through the WordPress plugins screen directly. You can also download the Web3 Access plugin at https://metapress.ca/download/.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Follow our <a href="https://metapress.ca/help/getting-started/" target="_blank">How It Works</a> guide.

<h4>Support</h4>

<p><a href="https://metapress.ca/faq/" target="_blank">See our FAQ</a></p>
<p>Additional support for this plugin at https://metapress.ca/contact/</p>


== Changelog ==

= 1.6.8 =

<ul>
<li>Added getGasPrice call for more accurate gas price estimates on transactions.</li>
</ul>

= 1.6.7 =

<ul>
<li>Bug fix when checking price for custom tokens not listed on Binance or CoinGecko.</li>
</ul>

= 1.6.3 =

<ul>
<li>Sepolia Test Network replaced Goerli</li>
</ul>

= 1.6.2 =

<ul>
<li>Try Catch added to display browser wallet errors</li>
</ul>

= 1.6.0 =

<ul>
<li>Updated deprecated ethereum provider chainId to RPC method</li>
</ul>

= 1.5.7 =

<ul>
<li>NFT Verification options added to restrict entire page content.</li>
</ul>

= 1.5.6 =

<ul>
<li>PHP session management updates</li>
<li>Wallet page refresh and token access updates.</li>
</ul>

= 1.5.5 =

<ul>
<li>Solana Wallet JS updates for when wallet is changed.</li>
<li>PHP session management updates to prevent headers already sent warning</li>
</ul>

= 1.5.4 =

<ul>
<li>New setting for custom Solana RPC URL incase mainnet-beta is returning 403 errors. Found under <strong>Web3 Access -> Wallet Addresses</strong></li>
</ul>

= 1.5.3 =

<ul>
<li>Fixed a bug where Product JS would not load if page settings were not set.</li>
</ul>

= 1.5.2 =

<ul>
<li>Tested up to version 6.2.2 readme updated.</li>
</ul>

= 1.5.1 =

<ul>
<li>Bug fix for address reference during verification process.</li>
</ul>

= 1.5.0 =

<ul>
<li>Products can now be set to subscriptions, requiring a renewal payment or NFT Verification after the expiration time.</li>
<li>Update to the payment options during checkout. New <strong>Wallet Payment</strong> and <strong>NFT Verification</strong> tabs let visitors know the 2 options without needing to scroll down page for NFT Verification.</li>
<li>New [metapress-subscriptions] shortcode for displaying visitors subscriptions on a page.</li>
<li>Email notifications for subscription renewal reminders.</li>
</ul>

= 1.4.9 =

<ul>
<li>WordPress tested version update 6.2.0</li>
</ul>

= 1.4.8 =

<ul>
<li>Redirect loop set token param fix</li>
<li>Network images URL update fix</li>
</ul>

= 1.4.7 =

<ul>
<li>Bug fix when adding new NFT contracts for verification. Token Type option was blank.</li>
</ul>

= 1.4.5 =

<ul>
<li>Added support for Solana Network</li>
<li>Wallet Address Management Updates</li>
<li>New Wallet Connection Options</li>
<li>Ropsten Network removed, Sepolia Network added for testing.</li>
</ul>

= 1.4.4 =

<ul>
<li>Contract updates</li>
</ul>

= 1.4.3 =

<ul>
<li>Session manager updates to prevent warning logs in debug mode.</li>
</ul>

= 1.4.2 =

<ul>
<li>Admin notice for creating checkout page for product purchase buttons to appear.</li>
</ul>

= 1.4.1 =

<ul>
<li>Session variables added to verify access token ownership.</li>
</ul>

= 1.4.0 =

<ul>
<li>REST API Endpoint bug fix.</li>
</ul>

= 1.3.8 =

<ul>
<li>MetaProducts wording updated to Web3 Products</li>
</ul>

= 1.3.7 =

<ul>
<li>NEW Minimum Balance field for NFT and Token Verification process. Optionally change the required minimum Tokens or NFTs wallets need to hold in order to access your content.</li>
<li>NFT Verification update to check for existing access tokens so visitors do not need to verify again if they have a valid access token.</li>
</ul>

= 1.3.6 =

<ul>
<li>Checkout page Missing Product ID message fix.</li>
<li>Checkout page redirect bug fix.</li>
</ul>

= 1.3.5 =

<ul>
<li>Tested up to WordPress version 6.0.1</li>
</ul>

= 1.3.4 =

<ul>
<li>CSS styling for connect wallet button added. (.metapress-login-notice button.metamask-connect-wallet)</li>
</ul>

= 1.3.3 =

<ul>
<li>Removed JSON encoded data for deprecated CSS editor.</li>
</ul>

= 1.3.2 =

<ul>
<li>Added Binance and CoinGecko price API enable / disable for custom tokens.</li>
<li>Added Price (USD) field for custom tokens incase Binance / CoinGecko cannot find a price.</li>
</ul>

= 1.3.1 =

<ul>
<li>Updated 30 second nonce validation to 1 hour.</li>
</ul>

= 1.3.0 =

<ul>
<li>Updated readme.txt file</li>
</ul>

= 1.2.9 =

<ul>
<li>Tested up to WordPress version 6.0.0</li>
<li>Moved jquery-ui-smoothness CSS for admin dashboard into plugin files instead of external URL.</li>
</ul>

= 1.2.8 =

<ul>
<li>Sanitize and Escaping updates.</li>
<li>Stable Tag added to readme.txt</li>
</ul>

= 1.2.7 =

<ul>
<li>New <strong>Restrict Website Access</strong> option under <strong>Web3 Access -> Access Settings</strong>. Allows you to restrict access to all pages and website content.</li>
<li>New <strong>Allow Page Access</strong> option for individual Pages, Posts and supported content types. Only applicable when Restrict Website Access is enabled.</li>
<li>Added price label on Product Purchase buttons.</li>
<li>Added a View Page As Admin button when logged in as administrator to view restrict content pages.</li>
<li>Fixed a bug where Pending Transaction URLs were not linked to the explorer URL.</li>
</ul>

= 1.2.6 =

<ul>
<li>Added support for Fantom (FTM) Network and Testnet.</li>
</ul>

= 1.2.5 =

<ul>
<li>Removed web3 provider isMetaMask check to allow for Coinbase and other browser wallets.</li>
</ul>

= 1.2.4 =

<ul>
<li>Check if web3 provider is not null bug fix.</li>
</ul>

= 1.2.3 =

<ul>
<li>Added support for Avalanche (AVAX) Network and Avalanche Fuji Test Network</li>
</ul>

= 1.2.2 =

<ul>
<li>Added <strong>Web3 Access -> Styling</strong> admin page to customize Theme, Accent Colour and add Custom CSS.</li>
</ul>

= 1.2.1 =

<ul>
<li>New Web3 Access Restricted Content shortcode added for those not using the Gutenberg Block Editor</li>
<li>[metapress-restricted-content products="{MetaProduct IDs comma separated}"] your restricted content [/metapress-restricted-content]</li>
</ul>

= 1.2.0 =

<ul>
<li>Added 1 Week and 1 Month options to Access Token expiration time setting.</li>
</ul>

= 1.1.9 =

<ul>
<li>Optional OpenSea Collection Slug parameter for MetaProduct NFT verification. If you are using a <a href="https://polygonscan.com/address/0x2953399124f0cbb46d2cbacd8a89cf0599974963" target="_blank">shared OpenSea contract address</a> for NFT verification, you should request an OpenSea API Key and include the collection slug in your MetaProduct assets. This will check ONLY your collection and not all assets on the shared contract address.</li>
</ul>

= 1.1.8 =

<ul>
<li>OpenSea API Key setting (optional) added. If you need to verify visitors own ANY ERC-1155 token within a smart contract, you can add your OpenSea API Key under <strong> Web3 Access -> Smart Contracts (NFTs)</strong></li>
<li>IMPORTANT: The Token Contract Address should be a unique address and not a shared collection address. For example, <a href="https://etherscan.io/address/0x495f947276749ce646f68ac8c248420045cb7b5e" target="_blank">this address</a> is a shared OpenSea address. Using a shared smart contract address may result in the NFT verification system returning true if a users address owns ANY asset that belongs to the shared address (smart contract).</li>
</ul>

= 1.1.7 =

<ul>
<li>Binance Smart Chain network added for Payments. (1% transaction fee)</li>
</ul>

= 1.1.6 =

<ul>
<li>New WooCommerce Filter setting added to hide WooCommerce Product Add To Cart buttons for products that require Web3 Access Products.</li>
</ul>

= 1.1.5 =

<ul>
<li>New Access Tokens Expire After setting under <strong>Web3 Access -> Settings</strong></li>
<li>Access Token Exists server request updated to check for NFT verification access tokens. Previously only checked for paid transaction access tokens.</li>
</ul>

= 1.1.4 =

<ul>
<li>Admin custom tokens JS fix.</li>
</ul>

= 1.1.3 =

<ul>
<li>MetaMask initial loading process improved and updated to prepare for custom Network support.</li>
<li>Networks admin page added which will be a feature included in Pro (Premium) version of plugin.</li>
<li>Binance Smart Chain (BSC) added by default and can now be used for NFT ownership verification.</li>
<li>External Javascript libraries moved to internal plugin files to prevent delayed Connect Wallet button functionality.</li>
<li>Token ratio converter updated to support additional Networks and Tokens.</li>
</ul>

= 1.1.2 =

<ul>
<li>Just adding review link in plugin admin dashboard. More updates coming soon!</li>
</ul>

= 1.1.1 =

<ul>
<li>Connect Wallet button added to prompt user to connect MetaMask wallet.</li>
</ul>

= 1.1.0 =

<ul>
<li>Security updates to sanitize input and escape output variables.</li>
<li>PHP short tags removed.</li>
</ul>

= 1.0.9 =

<ul>
<li>Bug fix for Product Price number_format not working on certain PHP versions.</li>
</ul>

= 1.0.8 =

<ul>
<li>Bug fix where Product Price could not be set to 0 or empty.</li>
</ul>

= 1.0.7 =

<ul>
<li>Language &amp; Text settings added to <strong>Web3 Access -> Language</strong> admin page.</li>
</ul>

= 1.0.6 =

<ul>
<li>Provide access to content based on visitors owning any NFT within a Collection.</li>
</ul>

= 1.0.5 =

<ul>
<li>Redirect to previous page on Checkout Shortcode. Redirects visitors to previous page after confirming NFT ownership for access to product.</li>
</ul>

= 1.0.4 =

<ul>
<li>Provide access to pages and content via NFT ownership verification.</li>
</ul>

= 1.0.3 =

<ul>
<li>Added support form custom tokens that exist on either Ethereum Mainnet or Polygon Mainnet. Add custom tokens under <strong>Web3 Access -> Tokens</strong> in your WordPress Dashboard.</li>
<li>Tokens must exist on either the Ethereum or Polygon Mainnet Network.</li>
<li>Tokens must use 18 decimals for values.</li>
</ul>

= 1.0.1 =

<ul>
<li>Plugin update testing.</li>
</ul>

= 1.0.0 =

<ul>
<li>Release</li>
</ul>

== Upgrade Notice ==

= 1.0.3 =

<ul>
<li>Added support form custom tokens that exist on either Ethereum Mainnet or Polygon Mainnet. Add custom tokens under <strong>Web3 Access -> Tokens</strong> in your WordPress Dashboard.</li>
<li>Tokens must exist on either the Ethereum or Polygon Mainnet Network.</li>
<li>Tokens must use 18 decimals for values.</li>
</ul>
