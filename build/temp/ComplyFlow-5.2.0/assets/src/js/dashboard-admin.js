/* Dashboard Admin JS: Charts + Dark Mode */
import Chart from 'chart.js/auto';

(function($){
  const cfg = window.complyflowDashboard || {};
  const root = document.querySelector('.complyflow-dashboard');
  
  // Store chart instances for destruction/recreation
  let charts = {
    moduleBreakdown: null,
    cookieCategory: null,
    accessibilitySeverity: null,
    complianceTrend: null
  };

  // Dark Mode Toggle
  function applyMode(mode){
    if(mode === 'dark'){ root.classList.add('cf-dark'); } else { root.classList.remove('cf-dark'); }
    localStorage.setItem('complyflowDashboardMode', mode);
    const toggle = document.getElementById('cf-dark-toggle-label');
    if(toggle){ toggle.textContent = mode === 'dark' ? cfg.i18n.lightMode : cfg.i18n.darkMode; }
  }
  function initDarkMode(){
    const saved = localStorage.getItem('complyflowDashboardMode') || 'light';
    applyMode(saved);
    const btn = document.getElementById('cf-dark-toggle');
    if(btn){
      btn.addEventListener('click', ()=>{
        const current = root.classList.contains('cf-dark') ? 'dark' : 'light';
        applyMode(current === 'dark' ? 'light' : 'dark');
      });
    }
  }

  // Charts
  function makeModuleBreakdown(){
    const el = document.getElementById('cf-module-breakdown-chart');
    if(!el || !cfg.stats?.compliance?.breakdown) return;
    if(charts.moduleBreakdown) { charts.moduleBreakdown.destroy(); }
    const labels = Object.values(cfg.stats.compliance.breakdown).map(b=>b.label);
    const data = Object.values(cfg.stats.compliance.breakdown).map(b=>b.score);
    
    // Color coding: blue (80-100), cyan (60-79), purple (40-59), orange (20-39), red (<20)
    const backgroundColor = data.map(v => {
      if (v >= 80) return 'rgba(37,99,235,0.85)';
      if (v >= 60) return 'rgba(14,165,233,0.75)';
      if (v >= 40) return 'rgba(139,92,246,0.75)';
      if (v >= 20) return 'rgba(249,115,22,0.80)';
      return 'rgba(220,38,38,0.85)';
    });
    
    charts.moduleBreakdown = new Chart(el.getContext('2d'), {
      type: 'bar',
      data: { labels, datasets: [{
        label: 'Module Score %',
        data,
        borderRadius: 6,
        backgroundColor
      }]},
      options: { 
        responsive:true, 
        maintainAspectRatio:false, 
        scales:{ y:{ beginAtZero:true, max:100, ticks: { callback: v => v + '%' } } }, 
        plugins:{ 
          legend:{ display:false },
          tooltip: {
            callbacks: {
              label: function(context) {
                return context.parsed.y + '% compliance';
              }
            }
          }
        } 
      }
    });
  }

  function makeComplianceTrend(){
    const el = document.getElementById('cf-compliance-trend-chart');
    if(!el || !cfg.stats?.trends) return;
    if(charts.complianceTrend) { charts.complianceTrend.destroy(); }
    
    charts.complianceTrend = new Chart(el.getContext('2d'), {
      type: 'line',
      data: {
        labels: cfg.stats.trends.dates,
        datasets: [{
          label: 'Compliance Score',
          data: cfg.stats.trends.scores,
          borderColor: 'rgba(59,130,246,1)',
          backgroundColor: 'rgba(59,130,246,0.1)',
          borderWidth: 2,
          fill: true,
          tension: 0.4,
          pointRadius: 3,
          pointHoverRadius: 5,
          pointBackgroundColor: 'rgba(59,130,246,1)',
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: { 
            beginAtZero: true, 
            max: 100,
            ticks: { callback: v => v + '%' }
          }
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function(context) {
                return 'Score: ' + context.parsed.y + '%';
              }
            }
          }
        }
      }
    });
  }

  function makeCookieCategory(){
    const el = document.getElementById('cf-cookie-category-chart');
    if(!el || !cfg.stats?.cookies?.by_category) return;
    if(charts.cookieCategory) { charts.cookieCategory.destroy(); }
    const map = cfg.stats.cookies.by_category;
    const labels = Object.keys(map);
    const data = Object.values(map);
    charts.cookieCategory = new Chart(el.getContext('2d'), {
      type: 'doughnut',
      data: { labels, datasets: [{
        data,
        backgroundColor: ['#2563eb','#3b82f6','#0ea5e9','#0284c7'],
        borderColor: '#ffffff',
        borderWidth: 2,
        hoverOffset: 6
      }]},
      options:{ plugins:{ legend:{ position:'bottom' } } }
    });
  }

  function makeAccessibilitySeverity(){
    const el = document.getElementById('cf-accessibility-severity-chart');
    if(!el || !cfg.stats?.accessibility) return;
    if(charts.accessibilitySeverity) { charts.accessibilitySeverity.destroy(); }
    const s = cfg.stats.accessibility;
    const labels = ['Critical','Serious','Moderate'];
    const data = [s.critical_count, s.serious_count, s.moderate_count];
    charts.accessibilitySeverity = new Chart(el.getContext('2d'), {
      type: 'polarArea',
      data:{ labels, datasets:[{ data, backgroundColor:['#dc2626','#f97316','#f59e0b'], borderWidth:1 }]},
      options:{ scales:{ r:{ beginAtZero:true } }, plugins:{ legend:{ position:'bottom' } } }
    });
  }

  // Refresh dashboard stats and recreate charts
  function refreshDashboard() {
    return $.post(cfg.ajaxUrl, { 
      action: 'complyflow_dashboard_refresh_stats', 
      nonce: cfg.nonce 
    }).done(function(resp) {
      if(resp && resp.success && resp.data.stats) {
        // Update cfg.stats with fresh data
        cfg.stats = resp.data.stats;
        
        // Update widget values
        updateWidgetValues(resp.data.stats);
        
        // Recreate all charts with new data
        makeModuleBreakdown();
        makeCookieCategory();
        makeAccessibilitySeverity();
        makeComplianceTrend();
      }
    }).fail(function() {
    });
  }
  
  // Update widget display values
  function updateWidgetValues(stats) {
    // Update compliance score
    const scoreEl = document.querySelector('.cf-compliance-score');
    if(scoreEl && stats.compliance) {
      scoreEl.textContent = Math.round(stats.compliance.score) + '%';
    }
    
    // Update DSR count
    const dsrEl = document.querySelector('.cf-stat-card:nth-child(2) .cf-stat-value');
    if(dsrEl && stats.dsr) {
      dsrEl.textContent = stats.dsr.total || 0;
    }
    
    // Update consent rate
    const consentEl = document.querySelector('.cf-stat-card:nth-child(3) .cf-stat-value');
    if(consentEl && stats.consent) {
      const rate = stats.consent.total > 0 ? 
        Math.round((stats.consent.accepted / stats.consent.total) * 100) : 0;
      consentEl.textContent = rate + '%';
    }
    
    // Update accessibility issues text
    const accessibilityTextEl = document.querySelector('.widget-accessibility .total-issues');
    if(accessibilityTextEl && stats.accessibility) {
      const total = stats.accessibility.total_issues || 0;
      const issueText = total === 1 ? 'total issue found' : 'total issues found';
      accessibilityTextEl.textContent = total + ' ' + issueText;
    }
    
    // Update compliance score circle color
    updateScoreCircleColor(stats.compliance?.score || 0);
  }
  
  // Set score circle color based on thresholds
  function updateScoreCircleColor(score) {
    const circle = document.querySelector('.score-circle');
    if (!circle) return;
    
    let level;
    if (score >= 100) level = 'perfect';       // 100% - Perfect dark green
    else if (score >= 96) level = 'excellent'; // 96-99% - Dark green  
    else if (score >= 90) level = 'good';      // 90-95% - Light green
    else if (score >= 70) level = 'warning';   // 70-89% - Orange
    else level = 'critical';                   // 0-69% - Red
    
    circle.setAttribute('data-score-level', level);
  }

  function init(){
    initDarkMode();
    makeModuleBreakdown();
    makeCookieCategory();
    makeAccessibilitySeverity();
    makeComplianceTrend();
    initQuickActions();
    
    // Set initial score circle color
    const scoreCircle = document.querySelector('.score-circle');
    if (scoreCircle) {
      const score = parseInt(scoreCircle.getAttribute('data-score') || '0', 10);
      updateScoreCircleColor(score);
    }
  }

  // Toast helper
  function toast(message,type='info'){
    let wrap = document.getElementById('cf-toast-wrap');
    if(!wrap){
      wrap = document.createElement('div');
      wrap.id = 'cf-toast-wrap';
      wrap.style.position='fixed';wrap.style.top='70px';wrap.style.right='18px';wrap.style.zIndex='10000';wrap.style.display='flex';wrap.style.flexDirection='column';wrap.style.gap='10px';
      document.body.appendChild(wrap);
    }
    const el = document.createElement('div');
    el.className = 'cf-toast cf-toast-' + type;
    el.style.padding='10px 14px';el.style.borderRadius='8px';el.style.fontSize='13px';el.style.fontWeight='500';el.style.boxShadow='0 4px 14px -4px rgba(30,58,138,.35)';el.style.background= type==='error' ? '#dc2626' : (type==='success' ? '#2563eb' : '#243c5a'); el.style.color='#fff';
    el.textContent = message; wrap.appendChild(el); setTimeout(()=>{el.style.opacity='0';el.style.transition='opacity .4s';setTimeout(()=>el.remove(),400);}, 4000);
  }

  function showScanResults(type, data) {
    const modal = document.getElementById('cf-scan-results-modal');
    const title = document.getElementById('cf-modal-title');
    const content = document.getElementById('cf-scan-results-content');
    const viewDetailsBtn = document.getElementById('cf-view-details');
    
    if (!modal) {
      console.error('Modal not found');
      return;
    }
    
    let html = '';
    
    if (type === 'accessibility') {
      title.textContent = 'Accessibility Scan Results';
      const score = data.score || 0;
      const summary = data.summary || {};
      const scoreClass = score >= 80 ? 'success' : score >= 50 ? 'warning' : 'error';
      const scoreIcon = score >= 80 ? '✓' : score >= 50 ? '⚠' : '✗';
      
      html = `
        <div class="cf-result-card">
          <div class="cf-result-header">
            <h3 class="cf-result-title">
              <span class="cf-result-icon ${scoreClass}">${scoreIcon}</span>
              WCAG 2.2 Compliance Score
            </h3>
            <div class="cf-result-score">${score}%</div>
          </div>
          <div class="cf-result-stats">
            <div class="cf-result-stat">
              <p class="cf-result-stat-value">${summary.total_issues || 0}</p>
              <p class="cf-result-stat-label">Total Issues</p>
            </div>
            <div class="cf-result-stat">
              <p class="cf-result-stat-value" style="color: #dc3545;">${summary.critical || 0}</p>
              <p class="cf-result-stat-label">Critical</p>
            </div>
            <div class="cf-result-stat">
              <p class="cf-result-stat-value" style="color: #ffc107;">${summary.serious || 0}</p>
              <p class="cf-result-stat-label">Serious</p>
            </div>
            <div class="cf-result-stat">
              <p class="cf-result-stat-value" style="color: #17a2b8;">${summary.moderate || 0}</p>
              <p class="cf-result-stat-label">Moderate</p>
            </div>
          </div>
        </div>
      `;
      
      if (viewDetailsBtn) {
        if (data.scan_id) {
          viewDetailsBtn.style.display = 'inline-block';
          viewDetailsBtn.onclick = () => {
            window.location.href = 'admin.php?page=complyflow-accessibility-results&scan_id=' + data.scan_id;
          };
        } else {
          console.warn('No scan_id returned, hiding view details button');
          viewDetailsBtn.style.display = 'none';
        }
      }
      
    } else if (type === 'cookie') {
      title.textContent = 'Cookie Scan Results';
      const cookies = data.cookies || [];
      const count = data.count || cookies.length;
      
      // Group by category
      const grouped = cookies.reduce((acc, cookie) => {
        const cat = cookie.category || 'other';
        if (!acc[cat]) acc[cat] = [];
        acc[cat].push(cookie);
        return acc;
      }, {});
      
      const categoryLabels = {
        necessary: 'Necessary',
        functional: 'Functional',
        analytics: 'Analytics',
        marketing: 'Marketing',
        other: 'Other'
      };
      
      html = `
        <div class="cf-result-card">
          <div class="cf-result-header">
            <h3 class="cf-result-title">
              <span class="cf-result-icon success">🍪</span>
              Auto-Detected Cookies & Trackers
            </h3>
            <div class="cf-result-score">${count}</div>
          </div>
          <div class="cf-result-stats">
            <div class="cf-result-stat">
              <p class="cf-result-stat-value">${(grouped.necessary || []).length}</p>
              <p class="cf-result-stat-label">Necessary</p>
            </div>
            <div class="cf-result-stat">
              <p class="cf-result-stat-value">${(grouped.functional || []).length}</p>
              <p class="cf-result-stat-label">Functional</p>
            </div>
            <div class="cf-result-stat">
              <p class="cf-result-stat-value">${(grouped.analytics || []).length}</p>
              <p class="cf-result-stat-label">Analytics</p>
            </div>
            <div class="cf-result-stat">
              <p class="cf-result-stat-value">${(grouped.marketing || []).length}</p>
              <p class="cf-result-stat-label">Marketing</p>
            </div>
          </div>
          <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 4px solid #f59e0b; padding: 12px; margin: 16px 0; border-radius: 6px;">
            <p style="margin: 0; color: #78350f; font-size: 13px; line-height: 1.5;">
              <strong>📋 Enhanced Scanner:</strong> Detects 25+ tracking services including Google Analytics, Facebook Pixel, TikTok, LinkedIn, Twitter, and more. For cookies from iframes or external widgets, use <strong>Add External Cookie</strong> to document them manually.
            </p>
          </div>
      `;
      
      // Show top cookies by category
      Object.keys(grouped).slice(0, 2).forEach(category => {
        const catCookies = grouped[category].slice(0, 5);
        if (catCookies.length > 0) {
          html += `
            <h4 style="margin: 16px 0 8px; font-size: 14px; color: var(--cf-dash-text); text-transform: uppercase; letter-spacing: 0.5px;">
              ${categoryLabels[category] || category}
            </h4>
            <ul class="cf-result-list">
          `;
          catCookies.forEach(cookie => {
            html += `
              <li>
                <span><strong>${cookie.name}</strong> - ${cookie.provider || 'Unknown'}</span>
                <span class="badge ${cookie.category || 'other'}">${cookie.category || 'other'}</span>
              </li>
            `;
          });
          html += '</ul>';
        }
      });
      
      html += '</div>';
      
      if (viewDetailsBtn) {
        viewDetailsBtn.style.display = 'inline-block';
        viewDetailsBtn.onclick = () => {
          window.location.href = 'admin.php?page=complyflow-cookies';
        };
      }
      
    } else if (type === 'full') {
      title.textContent = 'Full Site Scan Results';
      const aData = data.accessibility || {};
      const cData = data.cookie || {};
      const score = aData.score || 0;
      const summary = aData.summary || {};
      const cookieCount = cData.count || 0;
      const scoreClass = score >= 80 ? 'success' : score >= 50 ? 'warning' : 'error';
      const scoreIcon = score >= 80 ? '✓' : score >= 50 ? '⚠' : '✗';
      
      html = `
        <div class="cf-result-card">
          <div class="cf-result-header">
            <h3 class="cf-result-title">
              <span class="cf-result-icon ${scoreClass}">${scoreIcon}</span>
              Accessibility Compliance
            </h3>
            <div class="cf-result-score">${score}%</div>
          </div>
          <div class="cf-result-stats">
            <div class="cf-result-stat">
              <p class="cf-result-stat-value">${summary.total_issues || 0}</p>
              <p class="cf-result-stat-label">Issues</p>
            </div>
            <div class="cf-result-stat">
              <p class="cf-result-stat-value" style="color: #dc3545;">${summary.critical || 0}</p>
              <p class="cf-result-stat-label">Critical</p>
            </div>
            <div class="cf-result-stat">
              <p class="cf-result-stat-value" style="color: #ffc107;">${summary.major || 0}</p>
              <p class="cf-result-stat-label">Major</p>
            </div>
          </div>
        </div>
        
        <div class="cf-result-card">
          <div class="cf-result-header">
            <h3 class="cf-result-title">
              <span class="cf-result-icon success">🍪</span>
              Cookie & Tracker Detection
            </h3>
            <div class="cf-result-score">${cookieCount}</div>
          </div>
          <p style="color: var(--cf-dash-text-muted); font-size: 14px; margin: 12px 0 0;">Found ${cookieCount} cookies and tracking scripts across ${Object.keys((cData.cookies || []).reduce((acc, c) => ({...acc, [c.category]: 1}), {})).length} categories.</p>
        </div>
      `;
      
      if (viewDetailsBtn) {
        viewDetailsBtn.style.display = 'none';
      }
    }
    
    content.innerHTML = html;
    modal.style.display = 'flex';
    
    // Close handlers
    const closeButtons = modal.querySelectorAll('.cf-modal-close');
    closeButtons.forEach(btn => {
      btn.onclick = () => {
        modal.style.display = 'none';
      };
    });
    
    // Close on backdrop click
    const backdrop = modal.querySelector('.cf-modal-backdrop');
    if (backdrop) {
      backdrop.onclick = () => {
        modal.style.display = 'none';
      };
    }
    
    // Close on Escape key
    const escHandler = (e) => {
      if (e.key === 'Escape') {
        modal.style.display = 'none';
        document.removeEventListener('keydown', escHandler);
      }
    };
    document.addEventListener('keydown', escHandler);
  }

  function initQuickActions(){
    const scanBtn = document.getElementById('run-accessibility-scan');
    const exportBtn = document.getElementById('export-dsr-data');
    const cookieBtn = document.getElementById('scan-cookies');
    const fullScanBtn = document.getElementById('run-full-scan');
    if(scanBtn){
      scanBtn.addEventListener('click', ()=>{
        if(scanBtn.disabled) return;
        scanBtn.disabled = true;
        toast(cfg.i18n.accessibilityScan,'info');
        $.post(cfg.ajaxUrl, { action:'complyflow_run_accessibility_scan', nonce: cfg.adminNonce, url: cfg.siteUrl }, function(resp){
          scanBtn.disabled = false;
          if(resp && resp.success){ 
            showScanResults('accessibility', resp.data);
            // Refresh dashboard after scan completes
            refreshDashboard();
          } else { 
            toast(resp?.data?.message || cfg.i18n.error,'error'); 
          }
        }).fail((xhr, status, error)=>{ 
          scanBtn.disabled=false; 
          toast(cfg.i18n.error,'error'); 
        });
      });
    }
    if(exportBtn){
      exportBtn.addEventListener('click', ()=>{
        if(exportBtn.disabled) return;
        exportBtn.disabled = true; toast(cfg.i18n.exportingDSR,'info');
        $.post(cfg.ajaxUrl, { action:'complyflow_dashboard_export_dsr', nonce: cfg.nonce }, function(resp){
          exportBtn.disabled = false;
          if(resp && resp.success){
            const blob = new Blob([resp.data.csv], {type:'text/csv'});
            const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = resp.data.filename; a.click();
            toast(cfg.i18n.dsrExportDone + ' ('+ resp.data.count +')','success');
          } else { toast(resp?.data?.message || cfg.i18n.error,'error'); }
        }).fail(()=>{ exportBtn.disabled=false; toast(cfg.i18n.error,'error'); });
      });
    }
    if(cookieBtn){
      cookieBtn.addEventListener('click', ()=>{
        if(cookieBtn.disabled) return;
        cookieBtn.disabled = true; toast(cfg.i18n.cookieScan,'info');
        $.post(cfg.ajaxUrl, { action:'complyflow_scan_cookies', nonce: cfg.cookieNonce, url: cfg.siteUrl }, function(resp){
          cookieBtn.disabled = false;
          if(resp && resp.success){ 
            showScanResults('cookie', resp.data);
            // Refresh dashboard after scan completes
            refreshDashboard();
          } else { 
            const errorMsg = resp?.data?.message || cfg.i18n.error;
            toast(errorMsg, 'error'); 
          }
        }).fail((xhr, status, error)=>{ 
          cookieBtn.disabled=false; 
          let errorMsg = cfg.i18n.error;
          if (xhr.status === 403) {
            errorMsg = 'Security check failed. Please refresh the page and try again.';
          } else if (xhr.status === 500) {
            errorMsg = 'Server error. Please check the error log.';
          }
          toast(errorMsg, 'error'); 
        });
      });
    }
    if(fullScanBtn){
      fullScanBtn.addEventListener('click', ()=>{
        if(fullScanBtn.disabled) return;
        fullScanBtn.disabled = true; toast(cfg.i18n.fullScanStarted,'info');
        // Chain accessibility then cookies
        $.post(cfg.ajaxUrl, { action:'complyflow_run_accessibility_scan', nonce: cfg.adminNonce, url: cfg.siteUrl }, function(aResp){
          $.post(cfg.ajaxUrl, { action:'complyflow_scan_cookies', nonce: cfg.cookieNonce, url: cfg.siteUrl }, function(cResp){
            fullScanBtn.disabled = false;
            if(aResp?.success && cResp?.success){
              showScanResults('full', { accessibility: aResp.data, cookie: cResp.data });
              // Refresh dashboard after full scan completes
              refreshDashboard();
            } else {
              toast(aResp?.data?.message || cResp?.data?.message || cfg.i18n.error,'error');
            }
          }).fail((xhr, status, error)=>{ 
            fullScanBtn.disabled=false; 
            toast(cfg.i18n.error,'error'); 
          });
        }).fail((xhr, status, error)=>{ 
          console.error('Full scan - accessibility AJAX failed:', status, error, xhr.responseText);
          fullScanBtn.disabled=false; 
          toast(cfg.i18n.error,'error'); 
        });
      });
    }
   }  document.addEventListener('DOMContentLoaded', init);
})(jQuery);
