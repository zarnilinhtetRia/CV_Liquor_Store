<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>API Documentation </title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 90%;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            color: white;
        }

        .header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 300;
        }

        .api-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .api-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }

        .section-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .section-header i {
            font-size: 1.5rem;
        }

        .section-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .endpoint-list {
            padding: 0;
        }

        .endpoint-item {
            border-bottom: 1px solid #f1f5f9;
            padding: 0;
            transition: background-color 0.2s ease;
        }

        .endpoint-item:last-child {
            border-bottom: none;
        }

        .endpoint-item:hover {
            background-color: #f8fafc;
        }

        .endpoint-header {
            padding: 1.5rem 2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        .endpoint-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
        }

        .method-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            min-width: 60px;
            text-align: center;
        }

        .method-get { background: #10b981; color: white; }
        .method-post { background: #3b82f6; color: white; }
        .method-put { background: #f59e0b; color: white; }
        .method-delete { background: #ef4444; color: white; }

        .endpoint-path {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 1rem;
            font-weight: 500;
            color: #1e293b;
        }

        .endpoint-description {
            color: #64748b;
            font-size: 0.9rem;
            margin-left: 1rem;
        }

        .expand-icon {
            color: #94a3b8;
            transition: transform 0.2s ease;
        }

        .endpoint-item.expanded .expand-icon {
            transform: rotate(180deg);
        }

        .endpoint-details {
            padding: 0 2rem 1.5rem 2rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: none;
        }

        .endpoint-item.expanded .endpoint-details {
            display: block;
        }

        .request-sample {
            margin-top: 1rem;
        }

        .request-sample h4 {
            color: #374151;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .request-sample h4 i {
            color: #6b7280;
        }

        .code-block {
            background: #1e293b;
            color: #e2e8f0;
            padding: 1.5rem;
            border-radius: 8px;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.9rem;
            line-height: 1.5;
            overflow-x: auto;
            border: 1px solid #334155;
        }

        .code-block .json-key {
            color: #60a5fa;
        }

        .code-block .json-string {
            color: #34d399;
        }

        .code-block .json-number {
            color: #fbbf24;
        }

        .code-container {
            position: relative;
        }

        .copy-button {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: #374151;
            color: #e5e7eb;
            border: none;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-family: 'Inter', sans-serif;
        }

        .copy-button:hover {
            background: #4b5563;
            color: white;
        }

        .copy-button.copied {
            background: #10b981;
            color: white;
        }

        .copy-button i {
            font-size: 0.7rem;
        }

        .route-copy-button {
            background: #6b7280;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            margin-left: 0.5rem;
        }

        .route-copy-button:hover {
            background: #4b5563;
            color: white;
        }

        .route-copy-button.copied {
            background: #10b981;
            color: white;
        }

        .route-copy-button i {
            font-size: 0.6rem;
        }

        .footer {
            text-align: center;
            margin-top: 3rem;
            color: white;
            opacity: 0.8;
        }

        .footer p {
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .endpoint-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .endpoint-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .endpoint-description {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-code"></i> API Documentation</h1>
            <p>Complete API reference for Cargo Application</p>
        </div>

        <div class="api-section">
            <div class="section-header">
                <i class="fas fa-map-marker-alt"></i>
                <h2>Location Management APIs</h2>
            </div>
            <div class="endpoint-list">
                <div class="endpoint-item" onclick="toggleEndpoint(this)">
                    <div class="endpoint-header">
                        <div class="endpoint-info">
                            <span class="method-badge method-get">GET</span>
                            <span class="endpoint-path">/api/v1/location</span>
                            <span class="endpoint-description">Retrieve a list of all locations</span>
                        </div>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </div>
                </div>

                <div class="endpoint-item" onclick="toggleEndpoint(this)">
                    <div class="endpoint-header">
                        <div class="endpoint-info">
                            <span class="method-badge method-post">POST</span>
                            <span class="endpoint-path">/api/v1/location_register</span>
                            <span class="endpoint-description">Register a new location</span>
                        </div>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </div>
                    <div class="endpoint-details">
                        <div class="request-sample">
                            <h4><i class="fas fa-code"></i> Request Sample</h4>
                              <div class="code-container">
                                <button class="copy-button" onclick="copyToClipboard(this, 'location_register')">
                                    <i class="fas fa-copy"></i>
                                    <span>Copy</span>
                                </button>
                                <div class="code-block" id="location_register">
{</br />
    <span class="json-key">"name"</span>: <span class="json-string">"Main Office"</span>,<br />
    <span class="json-key">"phone"</span>: <span class="json-string">"+1234567890"</span>,<br />
    <span class="json-key">"address"</span>: <span class="json-string">"123 Business St, City, State 12345"</span><br />
}
                            </div>
                        </div>
                        </div>
                        <div class="request-sample">
                            <h4><i class="fas fa-info-circle"></i> Field Requirements</h4>
                            <div class="code-block">
                                <span class="json-key">"name"</span> - Location name (required)<br />
                                <span class="json-key">"phone"</span> - Contact phone number (optional)<br />
                                <span class="json-key">"address"</span> - Physical address (optional)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint-item" onclick="toggleEndpoint(this)">
                    <div class="endpoint-header">
                        <div class="endpoint-info">
                            <span class="method-badge method-put">PUT</span>
                            <span class="endpoint-path">/api/v1/location_update/{id}</span>
                            <span class="endpoint-description">Update an existing location by ID</span>
                        </div>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </div>
                    <div class="endpoint-details">
                        <div class="request-sample">
                            <h4><i class="fas fa-code"></i> Request Sample</h4>
                            <div class="code-container">
                                <button class="copy-button" onclick="copyToClipboard(this, 'location_update')">
                                    <i class="fas fa-copy"></i>
                                    <span>Copy</span>
                                </button>
                                <div class="code-block" id="location_update">
{<br />
    <span class="json-key">"name"</span>: <span class="json-string">"Updated Warehouse Name"</span>,<br />
    <span class="json-key">"address"</span>: <span class="json-string">"456 New Business St, City, Country"</span>,<br />
    <span class="json-key">"phone"</span>: <span class="json-string">"+1987654321"</span><br />
}
                                </div>
                            </div>
                        </div>
                        <div class="request-sample">
                            <h4><i class="fas fa-info-circle"></i> Field Requirements</h4>
                            <div class="code-block">
                                <span class="json-key">"name"</span> - Location name (required)<br />
                                <span class="json-key">"phone"</span> - Contact phone number (optional)<br />
                                <span class="json-key">"address"</span> - Physical address (optional)
                            </div>
                        </div>
                    </div>

                </div>

                <div class="endpoint-item" onclick="toggleEndpoint(this)">
                    <div class="endpoint-header">
                        <div class="endpoint-info">
                            <span class="method-badge method-get">GET</span>
                            <span class="endpoint-path">/api/v1/location_show/{id}</span>
                            <span class="endpoint-description">Retrieve a specific location by ID</span>
                        </div>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </div>
                </div>

                <div class="endpoint-item" onclick="toggleEndpoint(this)">
                    <div class="endpoint-header">
                        <div class="endpoint-info">
                            <span class="method-badge method-delete">DELETE</span>
                            <span class="endpoint-path">/api/v1/location_destroy/{id}</span>
                            <span class="endpoint-description">Delete a location by ID</span>
                        </div>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="api-section">
            <div class="section-header">
                <i class="fas fa-users"></i>
                <h2>User Management APIs</h2>
            </div>
            <div class="endpoint-list">
                <div class="endpoint-item" onclick="toggleEndpoint(this)">
                    <div class="endpoint-header">
                        <div class="endpoint-info">
                            <span class="method-badge method-get">GET</span>
                            <span class="endpoint-path">/api/v1/users</span>
                            <span class="endpoint-description">Retrieve a list of all users</span>
                        </div>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </div>
                </div>

                <div class="endpoint-item" onclick="toggleEndpoint(this)">
                    <div class="endpoint-header">
                        <div class="endpoint-info">
                            <span class="method-badge method-post">POST</span>
                            <span class="endpoint-path">/api/v1/user_register</span>
                            <span class="endpoint-description">Register a new user</span>
                        </div>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </div>
                    <div class="endpoint-details">
                        <div class="request-sample">
                            <h4><i class="fas fa-code"></i> Request Sample</h4>
                            <div class="code-container">
                                <button class="copy-button" onclick="copyToClipboard(this, 'user_register')">
                                    <i class="fas fa-copy"></i>
                                    <span>Copy</span>
                                </button>
                                <div class="code-block" id="user_register">
{ <br />
    <span class="json-key">"account_id"</span>: <span class="json-string">"001"</span>, <br />
    <span class="json-key">"type"</span>: <span class="json-string">"Staff"</span>, <br />
    <span class="json-key">"name"</span>: <span class="json-string">"John Doe"</span>, <br />
    <span class="json-key">"email"</span>: <span class="json-string">"john.doe@example.com"</span>, <br />
    <span class="json-key">"password"</span>: <span class="json-string">"password123"</span>, <br />
    <span class="json-key">"password_confirmation"</span>: <span class="json-string">"password123"</span>, <br />
    <span class="json-key">"father_name"</span>: <span class="json-string">"John Smith"</span>, <br />
    <span class="json-key">"phno"</span>: <span class="json-string">"1234567890"</span>, <br />
    <span class="json-key">"address"</span>: <span class="json-string">"123 Main St, City, Country"</span>, <br />
    <span class="json-key">"location"</span>: <span class="json-number">1</span>, <br />
    <span class="json-key">"social_media"</span>: <span class="json-string">"http://facebook.com/johndoe"</span>, <br />
    <span class="json-key">"profile_photo1"</span>: <span class="json-string">(file - jpg,jpeg,png)</span>, <br />
    <span class="json-key">"profile_photo2"</span>: <span class="json-string">(file - jpg,jpeg,png)</span>, <br />
    <span class="json-key">"profile_photo3"</span>: <span class="json-string">(file - jpg,jpeg,png)</span>, <br />
    <span class="json-key">"profile_photo4"</span>: <span class="json-string">(file - jpg,jpeg,png)</span> <br />

}
                                </div>
                            </div>
                        </div>
                        <div class="request-sample">
                            <h4><i class="fas fa-info-circle"></i> Field Requirements</h4>
                            <div class="code-block">
                                <span class="json-key">"account_id"</span> - Account identifier (required) <br />
                                <span class="json-key">"type"</span> - User type/role (required, string) <br />

                                <span class="json-key">"name"</span> - Full name (required, string) <br />
                                <span class="json-key">"email"</span> - Email address (required, valid email, unique) <br />
                                <span class="json-key">"password"</span> - Password (required, string, min 6 chars, must be confirmed) <br />
                                <span class="json-key">"father_name"</span> - Father's name (optional, string) <br />
                                <span class="json-key">"phno"</span> - Phone number (optional, string) <br />
                                <span class="json-key">"address"</span> - Address (optional, string) <br />
                                <span class="json-key">"location"</span> - Location ID (optional, integer) <br />
                                <span class="json-key">"social_media"</span> - Social media link (optional, string) <br />
                                <span class="json-key">"profile_photo1"</span> - Profile images (optional, jpg/jpeg/png only) <br />
                                <span class="json-key">"profile_photo2"</span> - Profile images (optional, jpg/jpeg/png only)  <br />
                                <span class="json-key">"profile_photo3"</span> - Profile images (optional, jpg/jpeg/png only) <br />
                                <span class="json-key">"profile_photo4"</span> - Profile images (optional, jpg/jpeg/png only)  <br />
                            </div>
                        </div>
                    </div>

                </div>

                <div class="endpoint-item" onclick="toggleEndpoint(this)">
                    <div class="endpoint-header">
                        <div class="endpoint-info">
                            <span class="method-badge method-put">PUT</span>
                            <span class="endpoint-path">/api/v1/user_update/{id}</span>
                            <span class="endpoint-description">Update an existing user by ID</span>
                        </div>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </div>
                    <div class="endpoint-details">
                        <div class="request-sample">
                            <h4><i class="fas fa-code"></i> Request Sample</h4>
                            <div class="code-container">
                                <button class="copy-button" onclick="copyToClipboard(this, 'user_update')">
                                    <i class="fas fa-copy"></i>
                                    <span>Copy</span>
                                </button>
                                <div class="code-block" id="user_update">
{ <br />
    <span class="json-key">"account_id"</span>: <span class="json-string">"001"</span>,<br />
    <span class="json-key">"type"</span>: <span class="json-string">"Manager"</span>,<br />
    <span class="json-key">"name"</span>: <span class="json-string">"John Doe Updated"</span>,<br />
    <span class="json-key">"email"</span>: <span class="json-string">"john.doe@example.com"</span>,<br />
    <span class="json-key">"password"</span>: <span class="json-string">"password123"</span>,<br />
    <span class="json-key">"password_confirmation"</span>: <span class="json-string">"password123"</span>,<br />
    <span class="json-key">"father_name"</span>: <span class="json-string">"John Smith"</span>,<br />
    <span class="json-key">"phno"</span>: <span class="json-string">"1234567890"</span>,<br />
    <span class="json-key">"address"</span>: <span class="json-string">"123 Main St, City, Country"</span>,<br />
    <span class="json-key">"location"</span>: <span class="json-number">1</span>,<br />
    <span class="json-key">"social_media"</span>: <span class="json-string">"http://facebook.com/johndoe"</span>,<br />
    <span class="json-key">"profile_photo1"</span>: <span class="json-string">(file - jpg,jpeg,png)</span>,<br />
    <span class="json-key">"profile_photo2"</span>: <span class="json-string">(file - jpg,jpeg,png)</span>,<br />
    <span class="json-key">"profile_photo3"</span>: <span class="json-string">(file - jpg,jpeg,png)</span>,<br />
    <span class="json-key">"profile_photo4"</span>: <span class="json-string">(file - jpg,jpeg,png)</span><br />
}
                                </div>
                            </div>
                        </div>
                        <div class="request-sample">
                            <h4><i class="fas fa-info-circle"></i> Field Requirements</h4>
                            <div class="code-block">
                                <span class="json-key">"account_id"</span> - Account identifier (required)<br />
                                <span class="json-key">"type"</span> - User type/role (required, string)<br />

                                <span class="json-key">"name"</span> - Full name (required, string)<br />
                                <span class="json-key">"email"</span> - Email address (required, valid email, unique)<br />
                                <span class="json-key">"password"</span> - Password (required, string, min 6 chars, must be confirmed)<br />
                                <span class="json-key">"father_name"</span> - Father's name (optional, string)<br />
                                <span class="json-key">"phno"</span> - Phone number (optional, string)<br />
                                <span class="json-key">"address"</span> - Address (optional, string)<br />
                                <span class="json-key">"location"</span> - Location ID (optional, integer)<br />
                                <span class="json-key">"social_media"</span> - Social media link (optional, string)<br />
                                <span class="json-key">"profile_photo1"</span> - Profile images (optional, jpg/jpeg/png only)<br />
                                <span class="json-key">"profile_photo2"</span> - Profile images (optional, jpg/jpeg/png only)<br />
                                <span class="json-key">"profile_photo3"</span> - Profile images (optional, jpg/jpeg/png only)<br />
                                <span class="json-key">"profile_photo4"</span> - Profile images (optional, jpg/jpeg/png only)<br />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint-item" onclick="toggleEndpoint(this)">
                    <div class="endpoint-header">
                        <div class="endpoint-info">
                            <span class="method-badge method-get">GET</span>
                            <span class="endpoint-path">/api/v1/user_show/{id}</span>
                            <span class="endpoint-description">Retrieve a specific user by ID</span>
                        </div>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </div>
                </div>

                <div class="endpoint-item" onclick="toggleEndpoint(this)">
                    <div class="endpoint-header">
                        <div class="endpoint-info">
                            <span class="method-badge method-delete">DELETE</span>
                            <span class="endpoint-path">/api/v1/user_destroy/{id}</span>
                            <span class="endpoint-description">Delete a user by ID</span>
                        </div>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </div>
                </div>

                <div class="endpoint-item" onclick="toggleEndpoint(this)">
                    <div class="endpoint-header">
                        <div class="endpoint-info">
                            <span class="method-badge method-post">POST</span>
                            <span class="endpoint-path">/api/v1/user_login</span>
                            <span class="endpoint-description">User login authentication</span>
                        </div>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </div>
                    <div class="endpoint-details">
                        <div class="request-sample">
                            <h4><i class="fas fa-code"></i> Request Sample</h4>
                            <div class="code-container">
                                <button class="copy-button" onclick="copyToClipboard(this, 'user_login')">
                                    <i class="fas fa-copy"></i>
                                    <span>Copy</span>
                                </button>
                                <div class="code-block" id="user_login">
{ <br />
<span class="json-key">"email"</span>: <span class="json-string">"john.doe@example.com"</span>, <br />
<span class="json-key">"password"</span>: <span class="json-string">"password123"</span>    <br />
}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




    </div>

    <script>
        function toggleEndpoint(element) {
            element.classList.toggle('expanded');
        }

        function copyToClipboard(button, elementId) {
            const codeElement = document.getElementById(elementId);
            const textToCopy = codeElement.textContent || codeElement.innerText;

            // Create a temporary textarea element
            const textarea = document.createElement('textarea');
            textarea.value = textToCopy;
            document.body.appendChild(textarea);
            textarea.select();

            try {
                // Copy the text to clipboard
                document.execCommand('copy');

                // Update button appearance
                const originalText = button.querySelector('span').textContent;
                const originalIcon = button.querySelector('i').className;

                button.querySelector('i').className = 'fas fa-check';
                button.querySelector('span').textContent = 'Copied!';
                button.classList.add('copied');

                // Reset button after 2 seconds
                setTimeout(() => {
                    button.querySelector('i').className = originalIcon;
                    button.querySelector('span').textContent = originalText;
                    button.classList.remove('copied');
                }, 2000);

            } catch (err) {
                console.error('Failed to copy text: ', err);
                // Fallback for modern browsers
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        const originalText = button.querySelector('span').textContent;
                        const originalIcon = button.querySelector('i').className;

                        button.querySelector('i').className = 'fas fa-check';
                        button.querySelector('span').textContent = 'Copied!';
                        button.classList.add('copied');

                        setTimeout(() => {
                            button.querySelector('i').className = originalIcon;
                            button.querySelector('span').textContent = originalText;
                            button.classList.remove('copied');
                        }, 2000);
                    });
                }
            } finally {
                // Remove the temporary textarea
                document.body.removeChild(textarea);
            }
        }

        function copyRouteToClipboard(route) {
            // Create a temporary textarea element
            const textarea = document.createElement('textarea');
            textarea.value = route;
            document.body.appendChild(textarea);
            textarea.select();

            try {
                // Copy the text to clipboard
                document.execCommand('copy');

                // Find the button that was clicked
                const buttons = document.querySelectorAll('.route-copy-button');
                buttons.forEach(button => {
                    if (button.getAttribute('onclick').includes(route)) {
                        const originalIcon = button.querySelector('i').className;
                        button.querySelector('i').className = 'fas fa-check';
                        button.classList.add('copied');

                        // Reset button after 2 seconds
                        setTimeout(() => {
                            button.querySelector('i').className = originalIcon;
                            button.classList.remove('copied');
                        }, 2000);
                    }
                });

            } catch (err) {
                console.error('Failed to copy route: ', err);
                // Fallback for modern browsers
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(route).then(() => {
                        const buttons = document.querySelectorAll('.route-copy-button');
                        buttons.forEach(button => {
                            if (button.getAttribute('onclick').includes(route)) {
                                const originalIcon = button.querySelector('i').className;
                                button.querySelector('i').className = 'fas fa-check';
                                button.classList.add('copied');

                                setTimeout(() => {
                                    button.querySelector('i').className = originalIcon;
                                    button.classList.remove('copied');
                                }, 2000);
                            }
                        });
                    });
                }
            } finally {
                // Remove the temporary textarea
                document.body.removeChild(textarea);
            }
        }
    </script>
</body>

</html>
