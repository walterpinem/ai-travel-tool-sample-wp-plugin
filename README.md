# AI Travel Tool Plugin for WordPress (Sample)
## A basic sample of the AI Travel Tool WordPress plugin.

This AI Travel Tool WordPress plugin leverages powerful AI API services from OpenAI, Groq, and OpenRouter to generate personalized travel itineraries right from your WordPress site. Designed with a user-friendly admin interface and dynamic shortcodes, it simplifies travel planning by combining real-time keyword data and advanced AI-generated content.

This plugin lets users to generate AI-powered travel planning. Let's contribute to this repository to making travel planning way smarter and more fun for everyone!

### Usage

![AI Travel Itinerary Generator](https://walterpinem.com/wp-content/uploads/2025/02/AI-Travel-Itinerary-Generator-tool-preview.png)

Use the following shortcode to display the AI travel generator tool anywhere on your WordPress site:

`[ai_travel_tool]`

#### Default AI models used
By default these are the AI models use in the plugin, but you can change them as you wish in the plugin's settings page:

- **OpenAI**: `gpt-4o-mini` (cheaper and good output) - Explore more [**AI models on OpenAI**](https://platform.openai.com/docs/models) to use 
- **OpenRouter**: `meta-llama/llama-3.3-70b-instruct:free` (free usage with limits) - Explore more [**AI models on OpenRouter**](https://openrouter.ai/models?max_price=0) to use 
- **Groq**: `llama-3.3-70b-versatile` (free usage with limits) - Explore more [**AI models on Groq**](https://console.groq.com/docs/models) to use

#### Default AI Prompt
The following is the default prompt, but you can change it anytime. Just keep using the `{destination}` and `{trip_type}` dynamic variables:

`You are a world-class travel expert. Craft a vivid, detailed itinerary for a {trip_type} trip to {destination} that blends iconic landmarks with off-the-beaten-path discoveries. Highlight must-see attractions, authentic local experiences, unique cuisine, and seasonal events to inspire an unforgettable journey.`

### Project Background
I worked with a client who owns a travel blog called [GetOutTrip](https://getouttrip.com/) to create an AI-powered travel tool. The blog is built on WordPress, so it was logical to develop an AI Travel WordPress Plugin that could integrate directly with their site. This plugin would allow for easy management and use of the tools.

### Development of AI Travel Tools
The client initially asked for one tool, but eventually, I developed a total of 9 AI tools and a [currency converter](https://getouttrip.com/currency-converter/). These tools include [AI Trip Ideas](https://getouttrip.com/ai-trip-ideas/), [AI Trip Itinerary Planner](https://getouttrip.com/trip-itinerary-planner/), [AI Road Trip Planner](https://getouttrip.com/road-trip-planner/), [AI Nearby Trip Ideas](https://getouttrip.com/nearby-trip-ideas/), [AI Trip Cost Estimator](https://getouttrip.com/trip-cost-estimator/), [AI Trip Length Guide](https://getouttrip.com/trip-length-guide/), [AI Cheap Travel Advisor](https://getouttrip.com/cheap-travel-advisor/), [AI Travel Packing List](https://getouttrip.com/travel-packing-list/), and [Travel Visa Requirements Checker](https://getouttrip.com/travel-visa-requirements-checker/). All of these tools were packaged into a single WordPress plugin, making it easy to use and manage.

Creating a shortcode interface for the AI Travel Tool plugin is all about making it easy to display the travel itinerary generator on any page or post. In my implementation, I registered a shortcode and conditionally enqueued the necessary CSS and JavaScript assets, ensuring that resources load only when the shortcode is present. 

The interface includes user-friendly form fields for entering the travel destination and selecting the trip type, as well as an optional dropdown for choosing an AI service. This design allows users to generate personalized travel itineraries directly from the front-end without any technical hassle.

I also incorporated robust error handling and security measures into the shortcode. The code checks if any API keys are configured and validates user inputs using nonce verification to prevent unauthorized access. 

By using output buffering, I ensure that the generated HTML is safely returned and rendered by WordPress. This approach not only keeps the plugin modular and maintainable but also ensures a seamless and efficient user experience for those looking to harness AI for travel planning.

For full tutorial and code workflow, read this post: [**Build AI Travel Tool Plugin for WordPress**](https://walterpinem.com/creating-an-ai-travel-tool-wordpress-plugin/)
