# Loading Error in Temply PWA

[tags]: # (trouble, load, err, doesn't, not, fatal, support, incomp, clea, cach, cookie, delet)
In some situations, Temply may not load, showing a Loading Error message.
This article aims to identify the cause and find a solution to this problem.

## Causes and troubleshooting steps
### Incompatible Browser
Temply PWA uses the latest Web technologies, so it can be executed only in a modern browser. Older browsers (for example, Internet Explorer, or any other browser dated to 2018) do not contain the necessary functionality and block Temply execution.

#### Troubleshooting
Install a modern browser, for example [Google Chrome](https://chrome.com) or [Mozilla Firefox](https://firefox.com). You can also use alternative versions of Temply, a list of which is located on the [main page of the project](/).

### Unstable Internet Connection
If your Internet connection is unstable, Temply executable files may not load correctly, resulting in a download error. This also includes the unexpected disappearance of the Internet connection.

#### Troubleshooting
Restart the application two or three times, making a small delay before closing the app. If this does not help, you should clear the application data.

#### Data Cleaning
##### Temply application
**Warning!** These actions will delete all saved data, such as the schedule
1. Click the Temply icon in the applications menu
2. Select or drag to **"App info"**
3. In the **"Storage"** section choose **Delete data** / **Clear storage** / **Reset**
4. If prompted to completely delete the data, confirm
5. Restart the application.

##### Chrome for phone
1. Open browser settings
2. Find and choose **Privacy**
3. Choose **Clear browsing data**
4. Select **Advanced** tab
5. Make sure the "Time range" below is set to **All time**
6. Remove the checkboxes from every item except **Cached images and files**
7. Tap the "Clear data" button

If the described steps have no effect: 

**Warning!** These actions will delete all saved data, such as the schedule
1. Open Temply in a browser
2. Click on the lock icon in the address bar
3. Select **Site Settings**
4. Click on "Clear and Reset"

##### Google Chrome or Chromium for PC
1. Copy the link: **chrome://settings/cookies/detail?site=app.temply.procsec.top**
2. Paste it into the address bar
3. In the window that opens, delete all items except ***Database storage**.
4. Restart the application
5. If these steps did not help, clear also the Database storage (**Warning!** This action will delete all saved data, such as the schedule)

##### Firefox for PC
Follow [instructions on the official Firefox site](https://support.mozilla.org/en-US/kb/storage)

##### Firefox for Phone
**Warning!** These actions will delete all saved data, such as the schedule
1. Open **Settings**
2. Select **Clear private data**
3. Check only the checkboxes next to **Cache** and **Offline website data**
4. Confirm the action

### Mistake in Temply
In unexpected cases the software may behave differently than intended. In Temply code, there may be a similar error, which leads to the inoperability of the entire application.

#### Troubleshooting
Click the **"Report"** button in the error message window, and the report will be sent for review to fix the problem.