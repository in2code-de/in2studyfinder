# Chatbot Plugin for in2studyfinder
## Overview

The chatbot plugin uses Mistral AI to help users find study courses based on their queries. It provides an interactive chat interface that can be added to any page.

## Prerequisites

- TYPO3 CMS 11 LTS or higher
- in2studyfinder extension installed
- Mistral AI API key (obtain from [https://mistral.ai/](https://mistral.ai/))

## Installation

1. Install the in2studyfinder extension via composer:
   ```bash
   composer require in2code/in2studyfinder
   ```

## Configuration

### API Key Setup

1. Go to the TYPO3 backend and navigate to **Admin Tools > Settings > Extension Configuration**.
2. Select **in2studyfinder** from the list.
3. Enter your Mistral AI API key in the **Mistral API Key** field.
4. Set the **Detail Page ID** to the page ID of your study course detail page.
5. Save the configuration.

### Creating Embeddings

Before the chatbot can function properly, you need to create embeddings for your study courses. This process analyzes your study course data and creates vector representations that the AI can use to find relevant courses.

Run the following command in your TYPO3 installation directory:

```bash
vendor/bin/typo3 in2studyfinder:createEmbeddings
```

This command will:
1. Fetch all study courses from the database
2. Create embeddings for each study course's title and teaser
3. Save the embeddings to a JSON file at `/fileadmin/in2studyfinder/chatbot/embeddings/studyCourses.json`

**Note:** You should run this command whenever you add, update, or delete study courses to keep the embeddings up to date. (Should be done by DataHandlerHook in the future)

## Adding the Chatbot to a Page

1. In the TYPO3 backend, navigate to the page where you want to add the chatbot.
2. Create a new content element and select the **Plugins** tab.
3. Choose the **Chatbot** plugin.
4. Configure the plugin settings:
   - **Display as widget**: Enable this to display the chatbot as a floating widget instead of inline.
   - **Chatbot Name**: Set the name of the chatbot (default: "ChatBot Assistant").
   - **Default Prompts**: Enter default prompts that will be suggested to users (one per line).
   - **Number of Results**: Set the maximum number of study courses to display in responses (1-10).
   - **Welcome Message**: Customize the welcome message shown when the chat is opened.
5. Save and close the content element.

## Customization
1. the system prompt can be overwritten via typoscript `plugin.tx_in2studyfinder_chatbot.settings.prompt.{languageTwoLetterIsoCode}`
2. To add a speakable Url for the chatbot api endpoint add the following snippet to your `siteconfiguration`:
```
routeEnhancers:
  PageTypeSuffix:
    type: PageType
    map:
      studyfinderAiChatbot.json: 1749648666
```

### Styling

The chatbot uses standard CSS classes that can be customized in your site package. The main classes are:

- `.chatbotwidget-container`: The widget container
- `.chatbot-container`: The inline chatbot container
- `.chatbot-messages`: The messages container
- `.chatbot-message`: Individual message
- `.chatbot-message-user`: User message
- `.chatbot-message-bot`: Bot message

### Templates

You can override the templates by copying the following files to your site package and adjusting them:

- `EXT:in2studyfinder/Resources/Private/Templates/Chat/Index.html`

## Troubleshooting

### Common Issues

1. **API Key Error**: If you see an error about a missing API key, make sure you've configured it correctly in the extension configuration.

2. **No Embeddings Found**: If the chatbot can't find any study courses, make sure you've run the embedding command and that there are study courses in your database.

3. **Rate Limiting**: Mistral AI has rate limits on API calls. If you're experiencing issues, check if you've exceeded your quota.

### Logging

The chatbot logs errors to the TYPO3 system log. In order to activate logging for EXT:in2studyfinder add the following snippet to your `ext_localconf.php`
```
    $GLOBALS['TYPO3_CONF_VARS']['LOG']['In2code']['In2studyfinder']['writerConfiguration'] = [
        \TYPO3\CMS\Core\Log\LogLevel::ERROR => [
            \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                'logFileInfix' => 'in2studyfinder',
            ]
        ],
    ];
```

## Best Practices

1. **Regular Updates**: Run the embedding command regularly to keep the embeddings up to date with your study course data.

2. **Prompt Engineering**: Craft default prompts that guide users to ask questions about study courses.

3. **Performance**: The chatbot makes API calls to Mistral AI, which can add latency. Consider using caching mechanisms for frequently asked questions.

## Support

If you encounter any issues or have questions about the chatbot plugin, please open an issue on GitHub.
