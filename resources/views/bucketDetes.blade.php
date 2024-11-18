@extends('layout.layout')




@section('content')
    <!-- Button to trigger the modal -->


    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Tables</h1>
        <p class="mb-4">Items in the bucket</a>.</p>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">DataTables</h6>
                <div class="query-row">

                    <div x-data="metadataComponent()" class="query-container">
                        <div id="query-builds">
                            <template x-for="(query, index) in queries" :key="index">
                                <div class="query-build mb-2">
                                    <select class="form-select" id="field-select" name="metadataField"
                                        x-model="query.metadataKey">
                                        <template x-for="(value, key) in metadataKeys" :key="key">
                                            <option x-bind:value="key" x-text="key"></option>
                                        </template>
                                    </select>
                                    <select class="form-select" x-model="query.operator">
                                        <option value="==">=</option>
                                        <option value=">">></option>
                                        <option value="<">
                                            < </option>
                                        <option value="<=">
                                            <= </option>
                                        <option value=">=">>=</option>
                                    </select>
                                    <input type="text" class="value-input" x-model="query.value"
                                        placeholder="Enter value">
                                    <template x-if="index > 0">
                                        <select class="form-select" x-model="query.operand" class="operand-select">
                                            <option value="and">AND</option>
                                            <option value="or">OR</option>
                                        </select>
                                    </template>
                                </div>
                            </template>
                            <div>
                                <label for="MaxKeys">MaxKeys</label>
                                <input type="text" id="MaxKeys" class="value-input" x-model="MaxKeys"
                                    placeholder="Enter value">
                                <label for="MaxKeys">Marker</label>
                                <input type="text" id="Marker" class="value-input" x-model="Marker"
                                    placeholder="Enter value">
                            </div>
                        </div>
                        <button class="btn-sm btn-primary" @click="addQueryBuild()">Add Condition</button>
                        <button class="btn-sm btn-primary" @click="logQueries()">Search Queries</button>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Last Modified</th>
                                <th>Size (byte)</th>
                                <th>Storage Class</th>
                                <th>Settings</th>
                            </tr>
                        </thead>
                        <tbody id='bucket-items-table'>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->

    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#bucketModal"
        onclick="fetchBucketMetadata(bucketName)">
        View Bucket Info
    </button>

    <button type="button" class="btn btn-danger" onclick="deleteBucket(bucketName)">
        Delete Bucket
    </button>

    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
        Upload File
    </button>

    <!-- Modal -->
    <div class="modal fade" id="bucketModal" tabindex="-1" role="dialog" aria-labelledby="bucketModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bucketModalLabel">Bucket Metadata</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="bucket-info">
                    <!-- The bucket metadata will be displayed here -->
                    Loading bucket information...
                </div>
                <div class="modal-body" id="bucket-info-1">
                    <!-- The bucket metadata will be displayed here -->
                    Loading bucket information...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal upload --}}
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Upload Form -->
                    <form id="uploadForm">
                        <div class="form-group">
                            <label for="key">File Key (S3 Object Name)</label>
                            <input type="text" class="form-control" id="key" placeholder="Enter file key"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="file">Choose file</label>
                            <input type="file" class="form-control-file" id="file" required>
                        </div>
                        <div class="form-group">
                            <label for="metadata">Metadata (JSON format)</label>
                            <textarea class="form-control" id="metadata" placeholder='{"Key1": "Value1", "Key2": "Value2"}'></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="uploadFile(bucketName)">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for viewing metadata -->
    <div class="modal fade" id="metadataModal" tabindex="-1" role="dialog" aria-labelledby="metadataModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="metadataModalLabel">Metadata</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="metadataContent">
                    <!-- Metadata content will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Editing Metadata -->
    <div class="modal fade" id="editMetadataModal" tabindex="-1" role="dialog"
        aria-labelledby="editMetadataModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMetadataModalLabel">Edit Metadata</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editMetadataForm">
                        <div class="form-group">
                            <label for="metadata">Metadata (JSON format)</label>
                            <textarea class="form-control" id="metadata-2" rows="4" placeholder='{"Key1": "Value1", "Key2": "Value2"}'
                                required></textarea>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="editMetadata(bucketName)">Save
                        changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Viewing Versions -->
    <div class="modal fade" id="viewVersionsModal" tabindex="-1" role="dialog"
        aria-labelledby="viewVersionsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewVersionsModalLabel">File Versions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Version ID</th>
                                <th>Last Modified</th>
                                <th>Size (bytes)</th>
                                <th>Is Latest</th>
                            </tr>
                        </thead>
                        <tbody id="versionsTableBody">
                            <!-- Versions will be inserted here dynamically -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Presigned URL -->
    <div class="modal fade" id="presignedUrlModal" tabindex="-1" role="dialog"
        aria-labelledby="presignedUrlModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="presignedUrlModalLabel">Generate Presigned URL</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="presignedUrlForm">
                        <div class="form-group">
                            <label for="duration">Duration (in minute)</label>
                            <input type="number" class="form-control" id="duration"
                                placeholder="Enter duration in minute(s)" required>
                        </div>
                    </form>
                    <div id="presignedUrlResult" class="mt-3" style="display:none; word-wrap: break-word;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="generatePresignedUrl()">Generate
                            URL</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('script')
        <script>
            const fullPath = window.location.pathname;
            const segments = fullPath.split('/');
            const bucketName = segments[segments.length - 1];


            // alpineJS
            function metadataComponent() {
                return {
                    metadataKeys: {},
                    queries: [{
                        metadataKey: '',
                        operator: '==',
                        value: '',
                        operand: '' // AND or OR
                    }],
                    MaxKeys: null,
                    Marker: '',

                    init() {
                        // Fetch metadata keys on component initialization
                        const apiUrl = `http://localhost:8011/${bucketName}/metadata/keys/`;

                        axios.get(apiUrl)
                            .then(response => {
                                this.metadataKeys = response.data.MetadataKeys || {};
                                console.log("Fetched metadataKeys:", this.metadataKeys);
                                if (Object.keys(this.metadataKeys).length > 0) {
                                    const firstKey = Object.keys(this.metadataKeys)[0];
                                    this.queries[0].metadataKey = firstKey;
                                }
                            })
                            .catch(error => {
                                console.error("Error fetching bucket metadata:", error);
                            });
                    },

                    addQueryBuild() {
                        // Push a new query object with operand included for additional queries
                        const firstKey = Object.keys(this.metadataKeys)[0];
                        this.queries.push({
                            metadataKey: firstKey || '',
                            operator: '==',
                            value: '',
                            operand: 'and' // Default operand for additional queries
                        });
                    },

                    logQueries() {
                        console.log("Current Queries:", this.queries);

                        let queryStrings = [];
                        let currentGroup = []; // to hold conditions within the same operand group
                        let lastOperand = null;
                        //gabung query yang memiliki value yg sama
                        let stringBuild = "";

                        this.queries.forEach((query, index) => {
                            let queryString = `${query.metadataKey}${query.operator}"${query.value}"`;
                            let operand = query.operand ? query.operand.toLowerCase() : null;

                            if (index == 0) {
                                //what the push should look like LastModified=="1"
                                currentGroup.push(queryString); //first one we just put it in
                                //if only one
                                if (index === this.queries.length - 1) {
                                    queryStrings.push(`(${currentGroup})`);
                                }
                            } else if (index == 1) { //if second we put the operand in
                                lastOperand = operand;
                                //what the push should look like and LastModified=="1"
                                currentGroup.push(`${operand} ${queryString}`);
                                if (index === this.queries.length - 1) {
                                    queryStrings.push(`(${currentGroup.join(' ')})`);
                                    // push this operand; this look (LastModified=="1" and LastModified=="1")
                                }
                            } else if (index > 1 && lastOperand == operand) {
                                //what the push should look like and LastModified=="1"
                                currentGroup.push(`${operand} ${queryString}`);
                                if (index === this.queries.length - 1) {
                                    queryStrings.push(`(${currentGroup.join(' ')})`);
                                    // push this operand; this look (LastModified=="1" and LastModified=="1")
                                }
                            } else if (index > 1 && lastOperand != operand) {
                                //if it different we combine the previous identical operand
                                queryStrings.push(`(${currentGroup.join(' ')})`);
                                //what the push should look like and LastModified=="1" and LastModified=="1" and LastModified=="1"
                                currentGroup = [];
                                // push this operand; this look (LastModified=="1" and LastModified=="1"), and,
                                queryStrings.push(`${operand}`);
                                currentGroup.push(`${queryString}`);
                                lastOperand = operand;
                                //if it's last push it all
                                if (index === this.queries.length - 1) {
                                    queryStrings.push(`(${currentGroup.join(' ')})`);
                                    // push this operand; this look (LastModified=="1" and LastModified=="1"), and, (LastModified=="1")
                                }
                            }

                        });
                        const fullQuery = queryStrings.join(' ');
                        // console.log(fullQuery);
                        // return 0;


                        console.log("Full Query String:", fullQuery);

                        const queryJson = {
                            Query: fullQuery,
                            MaxKeys: this.MaxKeys || null,
                            Marker: this.Marker || null
                        };

                        const apiUrl = `http://localhost:8011/bucketquery/${bucketName}`;

                        axios.post(apiUrl, queryJson, {
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => {
                                console.log("Query sent successfully:", response.data);
                                const rows = document.querySelectorAll("tr");
                                const itemKeys = new Set(response.data.map(item => item.Key));
                                rows.forEach(row => {
                                    if (!itemKeys.has(row.id)) {
                                        row.style.display = "none"; // Hide row if its ID doesn't match any item key
                                    } else {
                                        row.style.display = ""; // Ensure it's visible if it matches
                                    }
                                });
                            })
                            .catch(error => {
                                console.error("Error sending query:", error.response ? error.response.data : error.message);
                            });
                    }
                }
            }








            var currKey = '';
            //populate table
            const apiUrl = `http://localhost:8011/bucket/${bucketName}/items`;
            axios.get(apiUrl)
                .then(function(response) {
                    const items = response.data.Items;
                    const tableBody = document.getElementById('bucket-items-table');

                    // Clear any previous data in the table
                    tableBody.innerHTML = '';

                    // Loop through the items and create a row for each
                    items.forEach(function(item) {
                        const tableRow = `
                        <tr id="${item.Key}">
                            <td>${item.Key}</td>
                            <td>${item.LastModified}</td>
                            <td>${item.Size}</td>
                            <td>${item.StorageClass}</td>
                            <td>
                                <i class="fas fa-pen fa-fw" onclick="editMetadataModal('${item.Key}')"></i>
                                <i class="fas fa-eye fa-fw" onclick="viewMetadata('${item.Key}','${bucketName}')"></i>
                                <i class="fas fa-book fa-fw" onclick="viewVersions('${item.Key}', '${bucketName}')"></i>
                                <i class="fas fa-trash fa-sm" onclick="deleteItem('${item.Key}', '${bucketName}')"></i>
                               <i class="fas fa-link fa-sm" onclick="showPresignedUrlModal('${item.Key}', '${bucketName}')"></i>
                            </td>
                        </tr>
                    `;
                        // Append the row to the table body
                        tableBody.innerHTML += tableRow;
                    });
                })
                .catch(function(error) {
                    console.error("Error fetching bucket items:", error);
                    document.getElementById('bucket-items-table').innerHTML =
                        '<tr><td colspan="4">Error loading items.</td></tr>';
                });

            function FetchItemBasedonMetadata() {
                const apiUrl = `http://localhost:8011/bucket/${bucketName}/items`;
                axios.get(apiUrl)
                    .then(function(response) {
                        const items = response.data.Items;
                        const tableBody = document.getElementById('bucket-items-table');

                        // Clear any previous data in the table
                        tableBody.innerHTML = '';

                        // Loop through the items and create a row for each
                        items.forEach(function(item) {
                            const tableRow = `
                        <tr>
                            <td>${item.Key}</td>
                            <td>${item.LastModified}</td>
                            <td>${item.Size}</td>
                            <td>${item.StorageClass}</td>
                            <td>
                                <i class="fas fa-pen fa-fw" onclick="editMetadataModal('${item.Key}')"></i>
                                <i class="fas fa-eye fa-fw" onclick="viewMetadata('${item.Key}','${bucketName}')"></i>
                                <i class="fas fa-book fa-fw" onclick="viewVersions('${item.Key}', '${bucketName}')"></i>
                                <i class="fas fa-trash fa-sm" onclick="deleteItem('${item.Key}', '${bucketName}')"></i>
                               <i class="fas fa-link fa-sm" onclick="showPresignedUrlModal('${item.Key}', '${bucketName}')"></i>
                            </td>
                        </tr>
                    `;
                            // Append the row to the table body
                            tableBody.innerHTML += tableRow;
                        });
                    })
                    .catch(function(error) {
                        console.error("Error fetching bucket items:", error);
                        document.getElementById('bucket-items-table').innerHTML =
                            '<tr><td colspan="4">Error loading items.</td></tr>';
                    });
            }



            function fetchBucketMetadata(bucketName) {
                const apiUrl = `http://localhost:8011/bucket/${bucketName}`;
                axios.get(apiUrl)
                    .then(function(response) {
                        const bucketData = response.data.data;
                        const bucketInfoElement = document.getElementById('bucket-info');

                        // Dynamically generate HTML for specific key-value pairs
                        const metadataHtml = `
                <p><strong>Bucket:</strong> ${bucketData.Bucket}</p>
                <p><strong>Location:</strong> ${bucketData.Location}</p>
                <p><strong>Creation Time:</strong> ${bucketData.CreationTime}</p>
                <p><strong>Owner Display Name:</strong> ${bucketData.Owner.DisplayName}</p>
                <p><strong>Owner ID:</strong> ${bucketData.Owner.Id}</p>
                <p><strong>Versioning:</strong> ${bucketData.Versioning.Value}</p>
            `;

                        // Display the formatted metadata in the modal body
                        bucketInfoElement.innerHTML = metadataHtml;
                    })
                    .catch(function(error) {
                        console.error("Error fetching bucket metadata:", error);
                        document.getElementById('bucket-info').innerHTML = '<p>Error loading bucket information.</p>';
                    });
                const apiUrl1 = `http://localhost:8011/${bucketName}/metadata/keys/`;
                axios.get(apiUrl1)
                    .then(function(response) {
                        const bucketData = response.data; // Accessing the 'data' key in the response
                        const metadataKeys = bucketData.MetadataKeys;
                        const bucketInfoElement = document.getElementById('bucket-info-1');
                        console.log(metadataKeys);
                        if (metadataKeys && Object.keys(metadataKeys).length > 0) {
                            let metahtml = '<h5>Metadata Keys</h5><ul>';
                            for (const [key, value] of Object.entries(metadataKeys)) {
                                metahtml += `<li><strong>${key}:</strong> ${value}</li>`;
                            }
                            metahtml += '</ul>';
                            bucketInfoElement.innerHTML = metahtml;
                        }

                    })
                    .catch(function(error) {
                        console.error("Error fetching bucket metadata:", error);
                        document.getElementById('bucket-info-1').innerHTML = '<p>Error loading bucket information.</p>';
                    });

            }

            function deleteBucket(bucketName) {
                const apiUrl = `http://localhost:8011/bucket/${bucketName}`;

                axios.delete(apiUrl)
                    .then(function(response) {
                        const bucketData = response.data.data;
                        window.location.href = '/';
                    })
                    .catch(function(error) {
                        console.error("Error deleting bucket information.", error);
                    });
            }

            function uploadFile(bucketName) {
                // Get the form elements
                const key = document.getElementById('key').value;
                const file = document.getElementById('file').files[0];
                const metadata = document.getElementById('metadata').value;

                // Create FormData to send via Axios
                const formData = new FormData();
                formData.append('key', key);
                formData.append('file', file);
                formData.append('metadata', metadata);

                // API URL for file upload
                const apiUrl = `http://localhost:8011/bucket/${bucketName}`;

                // Perform the file upload via POST request
                axios.post(apiUrl, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then(response => {
                        // Handle success response (e.g., show success message)
                        alert('File uploaded successfully! Version ID: ' + response.data.VersionId);
                        location.reload();
                    })
                    .catch(error => {
                        // Handle error response
                        console.error('Error uploading file:', error);
                        alert('Failed to upload file. Please try again.');
                    });
            }

            function viewMetadata(key, bucketName) {
                const apiUrl = `http://localhost:8011/bucket/${bucketName}/${key}/metadata`;

                axios.get(apiUrl)
                    .then(function(response) {
                        const metadata = response.data.Metadata;
                        const lastModified = response.data.LastModified;
                        const contentType = response.data.ContentType;
                        const contentLength = response.data.ContentLength;

                        let metadataHtml = `<p><strong>Last Modified:</strong> ${lastModified}</p>`;
                        metadataHtml += `<p><strong>Content Type:</strong> ${contentType}</p>`;
                        metadataHtml += `<p><strong>Content Length:</strong> ${contentLength} bytes</p>`;

                        metadataHtml += '<p><strong>Custom Metadata:</strong></p>';
                        for (const key in metadata) {
                            metadataHtml += `<p>${key}: ${metadata[key]}</p>`;
                        }

                        // Insert the metadata into the modal body and show the modal
                        document.getElementById('metadataContent').innerHTML = metadataHtml;
                        $('#metadataModal').modal('show');
                    })
                    .catch(function(error) {
                        console.error('Error fetching metadata:', error);
                        alert('Failed to fetch metadata.');
                    });
            }

            function editMetadataModal(itemKey) {
                // Clear the textarea for new metadata input
                document.getElementById('metadata').value = '';
                // Store the itemKey in a global variable (or use a more secure method)
                currKey = itemKey;
                window.currentItemKey = itemKey;
                // Show the modal
                $('#editMetadataModal').modal('show');
            }

            function editMetadata(bucketName) {
                const metadataJson = document.getElementById('metadata-2').value;
                console.log('metadata');
                console.log(metadataJson);
                // Validate JSON format
                try {

                    const metadata = (metadataJson);

                    const apiUrl = `http://localhost:8011/bucket/${bucketName}/${currKey}/metadata`;

                    axios.put(apiUrl, {
                            Metadata: metadata
                        })
                        .then(response => {
                            console.log('Metadata updated:', response.data);
                            $('#editMetadataModal').modal('hide');
                            // Optionally refresh the metadata or table here
                        })
                        .catch(error => {
                            console.error('Error updating metadata:', error);
                            alert("Failed to update metadata. Please try again."); // User-friendly error message
                        });
                } catch (e) {
                    alert("Invalid JSON format. Please correct it.");
                }
            }

            function viewVersions(itemKey, bucketName) {
                const apiUrl = `http://localhost:8011/bucket/${bucketName}/${itemKey}/versions`;

                axios.get(apiUrl)
                    .then(response => {
                        const versions = response.data;
                        const tableBody = document.getElementById('versionsTableBody');

                        // Clear previous rows
                        tableBody.innerHTML = '';

                        // Populate table with versions data
                        versions.forEach(version => {
                            const row = document.createElement('tr');

                            row.innerHTML = `
                    <td>${version.VersionId}</td>
                    <td>${new Date(version.LastModified).toLocaleString()}</td>
                    <td>${version.Size}</td>
                    <td>${version.IsLatest ? 'Yes' : 'No'}</td>
                `;

                            tableBody.appendChild(row);
                        });

                        // Show the modal
                        $('#viewVersionsModal').modal('show');
                    })
                    .catch(error => {
                        console.error('Error fetching versions:', error);
                        alert('Error fetching versions');
                    });
            }

            function deleteItem(itemKey, bucketName) {
                const apiUrl = `http://localhost:8011/bucket/${bucketName}/${itemKey}/delete`;

                if (confirm('Are you sure you want to delete this item?')) {
                    axios.delete(apiUrl)
                        .then(response => {
                            console.log('Item deleted:', response.data);
                            alert('Item successfully deleted.');
                            location.reload();
                        })
                        .catch(error => {
                            console.error('Error deleting item:', error);
                            alert('Error deleting item. Please try again.');
                        });
                }
            }

            let selectedItemKey = '';
            let selectedBucketName = '';

            function showPresignedUrlModal(itemKey, bucketName) {
                selectedItemKey = itemKey;
                selectedBucketName = bucketName;
                $('#presignedUrlModal').modal('show');
            }

            function generatePresignedUrl() {
                const duration = document.getElementById('duration').value;

                if (!duration || isNaN(duration)) {
                    alert("Please enter a valid duration.");
                    return;
                }

                const apiUrl = `http://localhost:8011/bucket/${selectedBucketName}/${selectedItemKey}/presigned`;

                axios.post(apiUrl, {
                        Duration: parseInt(duration)
                    })
                    .then(response => {
                        const presignedUrl = response.data.url;
                        document.getElementById('presignedUrlResult').style.display = 'block';
                        document.getElementById('presignedUrlResult').innerHTML =
                            `
                         <strong>Presigned URL:</strong>
                            <a href="${presignedUrl}" target="_blank">${presignedUrl}</a>`;
                    })
                    .catch(error => {
                        console.error('Error generating presigned URL:', error);
                        alert('Failed to generate presigned URL. Please try again.');
                    });
            }
        </script>
    @endsection