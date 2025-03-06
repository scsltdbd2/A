// GitHub API configuration
const GITHUB_API_URL = 'https://api.github.com';
let githubToken = localStorage.getItem('githubToken') || '';
let currentPage = 1;
let totalPages = 1;
let currentSearchTerm = '';
let currentSearchType = '';
let resultsPerPage = 10;

// DOM Elements
const searchForm = document.getElementById('searchForm');
const searchInput = document.getElementById('search');
const searchType = document.getElementById('type');
const resultsContainer = document.getElementById('resultsContainer');
const searchResults = document.getElementById('searchResults');
const loadingIndicator = document.getElementById('loadingIndicator');
const resultsPerPageSelect = document.getElementById('resultsPerPage');
const prevPageBtn = document.getElementById('prevPage');
const nextPageBtn = document.getElementById('nextPage');
const pageInfo = document.getElementById('pageInfo');
const goToPageInput = document.getElementById('goToPage');
const goToPageBtn = document.getElementById('goToPageBtn');

// Token Management Elements
const githubTokenInput = document.getElementById('githubToken');
const validateTokenBtn = document.getElementById('validateTokenBtn');
const tokenStatus = document.getElementById('tokenStatus');
const tokenInfoBtn = document.getElementById('tokenInfoBtn');
const tokenInfoModal = document.getElementById('tokenInfoModal');
const tokenInfoCloseBtn = document.querySelector('.tokenInfoCloseBtn');

// Initialize token input
if (githubToken) {
    githubTokenInput.value = githubToken;
    validateGitHubToken(githubToken, false); // Validate silently on load
}

// Token Info Modal
tokenInfoBtn.addEventListener('click', () => {
    tokenInfoModal.classList.remove('hidden');
});

tokenInfoCloseBtn.addEventListener('click', () => {
    tokenInfoModal.classList.add('hidden');
});

// Close modal when clicking outside
tokenInfoModal.addEventListener('click', (e) => {
    if (e.target === tokenInfoModal) {
        tokenInfoModal.classList.add('hidden');
    }
});

// Token Validation
validateTokenBtn.addEventListener('click', async () => {
    const token = githubTokenInput.value.trim();
    await validateGitHubToken(token, true);
});

async function validateGitHubToken(token, showMessage = true) {
    if (!token) {
        if (showMessage) {
            showTokenStatus('Please enter a GitHub token', 'error');
        }
        return false;
    }

    try {
        tokenStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validating token...';
        tokenStatus.className = 'text-sm text-blue-600';

        const response = await fetch(`${GITHUB_API_URL}/user`, {
            headers: {
                'Authorization': `token ${token}`
            }
        });

        if (response.ok) {
            const data = await response.json();
            githubToken = token;
            localStorage.setItem('githubToken', token);
            if (showMessage) {
                showTokenStatus(`✓ Token validated successfully! Connected as ${data.login}`, 'success');
            }
            return true;
        } else {
            throw new Error(`Invalid token (${response.status})`);
        }
    } catch (error) {
        if (showMessage) {
            showTokenStatus(`✗ ${error.message}`, 'error');
        }
        return false;
    }
}

function showTokenStatus(message, type) {
    tokenStatus.textContent = message;
    tokenStatus.className = `text-sm ${type === 'error' ? 'text-red-600' : 'text-green-600'}`;
}

// Get Headers with Token
function getHeaders() {
    const headers = new Headers();
    if (githubToken) {
        headers.append('Authorization', `token ${githubToken}`);
    }
    return headers;
}

// Event Listeners
searchForm.addEventListener('submit', handleSearch);
resultsPerPageSelect.addEventListener('change', handleResultsPerPageChange);
prevPageBtn.addEventListener('click', () => navigateToPage(currentPage - 1));
nextPageBtn.addEventListener('click', () => navigateToPage(currentPage + 1));
goToPageBtn.addEventListener('click', handleGoToPage);

// Search Handler
async function handleSearch(e) {
    e.preventDefault();
    currentSearchTerm = searchInput.value.trim();
    currentSearchType = searchType.value;
    currentPage = 1;
    await performSearch();
}

// Results Per Page Change Handler
async function handleResultsPerPageChange() {
    resultsPerPage = parseInt(resultsPerPageSelect.value);
    if (currentSearchTerm) {
        await performSearch();
    }
}

// Go To Page Handler
async function handleGoToPage() {
    const pageNum = parseInt(goToPageInput.value);
    if (pageNum && pageNum > 0 && pageNum <= totalPages) {
        await navigateToPage(pageNum);
    } else {
        alert('Please enter a valid page number');
    }
}

// Page Navigation
async function navigateToPage(page) {
    if (page > 0 && page <= totalPages) {
        currentPage = page;
        await performSearch();
    }
}

// Search Implementation
async function performSearch() {
    try {
        showLoading(true);
        const endpoint = getSearchEndpoint();
        const response = await fetch(endpoint, {
            headers: getHeaders()
        });
        
        if (response.status === 401 || response.status === 403) {
            throw new Error('GitHub API rate limit exceeded. Please add a valid GitHub token to increase the limit.');
        }
        
        if (!response.ok) {
            throw new Error(`GitHub API error: ${response.status}`);
        }

        const data = await response.json();
        totalPages = Math.ceil(data.total_count / resultsPerPage);
        
        updatePagination();
        displayResults(data.items);
    } catch (error) {
        console.error('Search error:', error);
        displayError(error.message || 'An error occurred while searching. Please try again.');
    } finally {
        showLoading(false);
    }
}

// Get Search Endpoint Based on Type
function getSearchEndpoint() {
    const params = new URLSearchParams({
        q: currentSearchTerm,
        per_page: resultsPerPage,
        page: currentPage
    });

    switch (currentSearchType) {
        case 'repositories':
            return `${GITHUB_API_URL}/search/repositories?${params}`;
        case 'code':
            return `${GITHUB_API_URL}/search/code?${params}`;
        case 'commits':
            return `${GITHUB_API_URL}/search/commits?${params}`;
        default:
            return `${GITHUB_API_URL}/search/repositories?${params}`;
    }
}

// Display Results
function displayResults(items) {
    searchResults.innerHTML = '';
    
    if (!items || items.length === 0) {
        searchResults.innerHTML = '<p class="text-gray-700">No results found.</p>';
        return;
    }

    items.forEach(item => {
        const resultElement = createResultElement(item);
        searchResults.appendChild(resultElement);
    });
}

// Create Result Element
function createResultElement(item) {
    const li = document.createElement('li');
    li.className = 'border-b pb-4';

    if (currentSearchType === 'repositories') {
        li.innerHTML = `
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-bold">${item.name}</h3>
                    <p class="text-gray-700">${item.description || 'No description available'}</p>
                    <div class="flex space-x-4 mt-2">
                        <span class="text-gray-700"><i class="fas fa-star"></i> ${item.stargazers_count}</span>
                        <span class="text-gray-700"><i class="fas fa-code-branch"></i> ${item.forks_count}</span>
                        <span class="text-gray-700"><i class="fas fa-code"></i> ${item.language || 'Unknown'}</span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="${item.html_url}" target="_blank" class="text-blue-600 hover:underline">
                        <i class="fab fa-github"></i> View
                    </a>
                    <a href="${item.html_url}/archive/refs/heads/main.zip" class="text-blue-600 hover:underline">
                        <i class="fas fa-download"></i> Download
                    </a>
                    <a href="explore.html?repo=${item.full_name}" class="text-blue-600 hover:underline">
                        <i class="fas fa-search"></i> Explore
                    </a>
                </div>
            </div>
        `;
    } else {
        li.innerHTML = `
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-bold">${item.repository?.full_name || 'Unknown Repository'}</h3>
                    <p class="text-gray-700">${item.path || item.commit?.message || ''}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="${item.html_url}" target="_blank" class="text-blue-600 hover:underline">
                        <i class="fab fa-github"></i> View
                    </a>
                    <a href="explore.html?repo=${item.repository?.full_name}" class="text-blue-600 hover:underline">
                        <i class="fas fa-search"></i> Explore
                    </a>
                </div>
            </div>
        `;
    }

    return li;
}

// Update Pagination
function updatePagination() {
    pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
    prevPageBtn.disabled = currentPage === 1;
    nextPageBtn.disabled = currentPage === totalPages;
    goToPageInput.max = totalPages;
}

// Loading State
function showLoading(show) {
    loadingIndicator.classList.toggle('hidden', !show);
