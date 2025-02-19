# YouTube Playlist Reverser

A web-based tool that allows users to load YouTube playlists and change the playback order. This tool sorts the playlist videos by upload date, video title, and duration.

![page screenshot](ytplr-screenshot.jpg)

## Features

- **Custom Sorting Options:**
	- Oldest First.
	- Newest First.
	- A-Z.
	- Z-A.
	- Shortest First.
	- Longest First.
	- Most/Least viewed
 - **Randomize Button:** Shuffles the playlist order with every press.
- **Video Description Display:** Displays the description of the currently playing video.
- **Scroll to Current Video:** Automatically scrolls the current video in the playlist into view, with button to manually scroll to it as well.
- **Next/Previous Video Navigation:** Buttons to move to the next or previous video in the playlist.
- **Shareable Links:** The page URL preserves the playlist ID and the currently selected video ID, so the link for what you're watching can be copied from your browser's address bar, shared and loaded directly.

## Installation

1. Clone the repository or download the source code.
	 ```bash
	 git clone https://github.com/sean1138/yt-playlist-reverser.git
	 ```

2. Set up a local web server (e.g., Apache, Nginx, whatever). Place the project files in the server's document root.

3. Obtain a YouTube Data API v3 key:
	 - Go to the [Google Cloud Console](https://console.cloud.google.com/).
	 - Create a new project or select an existing one.
	 - Enable the YouTube Data API v3 for your project.
	 - Create an API key and note it down.

4. Configure the API key:
	 - Replace `EXAMPLEKEY` in the `ytplr-EXAMPLE.php` file with your actual API key and remove `-EXAMPLE` from the filename.

5. Start the web server and navigate to the project in your browser:
	 ```
	 http://localhost/yt-playlist-reverser/
	 ```

## Usage

1. Paste a YouTube playlist URL into the text input box and click "Load Playlist" button or hit the enter key.
2. Use the dropdown menu to select your desired playback order:
	 - Oldest First.
	 - Newest First.
	 - A-Z.
	 - Z-A.
	 - Shortest First.
	 - Longest First.
	 - Most/Least viewed
3. Click on any video in the playlist to start playback.
4. Use the Next/Previous buttons to navigate through the playlist.
5. Click the "Scroll to Current Video" button to quickly bring the currently playing video into view within the playlist if you've scrolled away from it.

## Folder Structure

```
project-folder/
├── index.html          # Main HTML file
├── ytplr.css           # Styling for the page
├── ytplr-EXAMPLE.php   # Backend script for API calls
├── README.md           # Project documentation
```

## Known Issues

- Playlists that are private or unavailable cannot be loaded.
- Exceeding the YouTube Data API quota may result in temporary unavailability.
- Layout is responsive but not friendly on small devices.

## Acknowledgments

- [YouTube Data API v3 Documentation](https://developers.google.com/youtube/v3/docs).
- ChatGPT 4o wrote pretty much all of the javascript, php, and most of this readme file.
- Inspiration from http://youtube.nestharion.de/ which hasn't worked in a few years now probably.
