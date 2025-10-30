from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch()
    page = browser.new_page()
    page.set_viewport_size({"width": 1280, "height": 720})
    page.goto("http://localhost:8000/index.html")

    # Wait for the navigation to complete
    page.wait_for_load_state("networkidle")

    # Click the "Conheça Mais" button in the CRMC section
    page.click('button:has-text("Conheça Mais")')

    # Click the "Acervo" button in the modal
    page.click('button:has-text("Acervo")')

    # Wait for the archive page to load
    page.wait_for_selector("select#filter-type")

    # Apply a filter
    page.select_option("select#filter-type", "Documento")

    # Wait for the results to load
    page.wait_for_selector("text=Documento Histórico 1")

    # Take a screenshot
    page.screenshot(path="jules-scratch/verification/verification.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)
