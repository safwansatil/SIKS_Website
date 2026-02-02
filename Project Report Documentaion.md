# **Product Requirements Document (PRD)**

## **IUT-SIKS Web Portal Migration**

| Project Name | IUT-SIKS Web Portal Migration |
| :---- | :---- |
| **Version** | 1.2 |
| **Status** | Approved for Development |
| **Target Platform** | IUT Enterprise Server (PHP 7.4+/Apache) |
| **Stakeholders** | IUT-SIKS Committee, University IT Administration |

## **1\. Executive Summary**

The Society of Islamic Knowledge Seekers (IUT-SIKS) requires the deployment of a centralized, university-hosted web portal. This platform will function as the primary interface for disseminating prayer schedules, managing organizational events, and facilitating student engagement.

The incumbent prototype, developed using the React framework and hosted on Vercel, is incompatible with the university's legacy infrastructure. Consequently, the application requires re-engineering into a server-side rendered PHP application. This migration must preserve the existing responsive design specifications while adhering to strict server constraints and professional content standards.

## **2\. Problem Statement**

* **Infrastructure Incompatibility:** The current React/Next.js architecture is unsupported by the university's PHP-based hosting environment.  
* **Operational Inefficiency:** The current update process requires code-level intervention, necessitating a decoupled content management solution for administrative personnel.  
* **Domain Isolation:** External hosting fragments the society's digital presence from the official university domain ecosystem.

## **3\. Strategic Objectives**

1. **Architecture Migration:** Re-platform the application from a client-side JavaScript framework to a native PHP architecture.  
2. **Design Compliance:** Replicate the approved User Interface (UI) design, specifically regarding the Emerald Green color system, modal interactions, and tabbed navigation structures.  
3. **Deployment Optimization:** Implement a file-based deployment strategy that eliminates build-step dependencies (e.g., Node.js, NPM).  
4. **Data Synchronization:** Integrate an external data synchronization layer using Google Sheets to facilitate remote content updates.  
5. **Professional Standards:** Ensure all UI text, metadata, and status indicators utilize formal English. **Emoji usage is strictly prohibited across the application.**

## **4\. Technical Architecture**

### **4.1. Technology Stack**

* **Backend:** PHP (Minimum Version 7.4).  
* **Frontend:** HTML5, Tailwind CSS (CDN delivery), FontAwesome (Iconography).  
* **Data Persistence:**  
  * **External Source:** Google Sheets (CSV Export API).  
  * **Caching Layer:** Local filesystem storage (prayer\_cache.json) with a 3600-second expiration policy.  
  * **Configuration:** PHP Array constants for static assets and navigational elements.

### **4.2. Directory Structure**

The application must adhere to the following modular structure to ensure maintainability:

/public\_html  
  ├── css/               \# Stylesheets and Design Tokens  
  ├── includes/          \# Backend Logic and Partials  
  │    ├── config.php    \# Business Logic: API Handling, Data Constants  
  │    ├── header.php    \# Global Navigation and Document Head  
  │    └── footer.php    \# Global Footer and Scripts  
  ├── index.php          \# Primary Controller  
  └── prayer\_cache.json  \# System-generated Cache File

## **5\. Functional Specifications**

### **5.1. User Interface Guidelines**

* **Color System:** The application must utilize the emerald-500 to emerald-600 spectrum from the Tailwind CSS palette.  
* **Navigation Components:**  
  * **Tab Interface:** Event categorization must be implemented via a Javascript-controlled tab system (Categories: All, Community, Sports).  
  * **Modal Interfaces:** Event details must render within a centered modal window featuring a backdrop blur effect (backdrop-filter: blur).  
* **Visual Feedback:** Status indicators must utilize CSS transitions for state changes. Animated elements must be subtle and professional.

### **5.2. Prayer Schedule Module**

* **Data Ingestion:** The system must retrieve data from a designated Google Sheet via HTTP request.  
* **Performance:** The system must check for a valid local cache file before initiating external network requests.  
* **Display Logic:** The interface must programmatically identify and highlight the current prayer time based on the Asia/Dhaka timezone.

### **5.3. Event Management Module**

* **Grid Layout:** Events shall be displayed in a responsive grid layout.  
* **Detail View:** User interaction with an event card triggers the detail modal.  
* **Content Restrictions:** All event descriptions and titles must be drafted in formal English.

## **6\. Integration & Configuration**

### **6.1. Google Sheets Integration**

* **Access Method:** Public CSV Export.  
* **Source URI:** https://docs.google.com/spreadsheets/d/1oD22Op0-b0D5tgNqZFbDWJPaju0mQ\_H234Rx0ZgRtKM/edit?usp=sharing

### **6.2. Contact & Geolocation**

* **Social Media Integration:**  
  * **YouTube:** https://www.youtube.com/@IUTSIKSOfficial  
  * **Facebook:** Link to official page.  
  * **Instagram:** **disabled/removed**.  
* **Geolocation:**  
  * **Address:** "Islamic University of Technology, Board Bazar, Gazipur-1704".  
  * **Map Integration:** The footer must contain a direct hyperlink to Google Maps coordinates for the IUT campus.

## **7\. Security & Deployment**

* **FileSystem Permissions:** The includes/ directory requires write permissions (755 or 775\) to enable caching operations.  
* **Network Protocols:** The backend must support both file\_get\_contents and cURL (with CURLOPT\_FOLLOWLOCATION enabled) to ensure compatibility with varying server configurations.

## **8\. User Acceptance Testing (UAT) Criteria**

1. **Visual Consistency Verification:** The PHP implementation must be visually indistinguishable from the approved design mockups.  
2. **Hyperlink Functional Testing:** Verify the functionality of the YouTube integration and confirm the absence of Instagram links.  
3. **Geolocation Verification:** Confirm the "Get Directions" link accurately targets the IUT Board Bazar campus on Google Maps.  
4. **Tone & Content Audit:** Validate that all text is professional and free of emojis or informal colloquialisms.