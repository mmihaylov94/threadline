<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Report Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reportForm" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="content_type" id="report-content-type">
                <input type="hidden" name="content_id" id="report-content-id">
                
                <div class="modal-body">
                    <p class="mb-3">Please select which community guidelines were violated:</p>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="guideline_violations[]" value="be_respectful" id="violation-respectful">
                            <label class="form-check-label" for="violation-respectful">
                                <strong>Be Respectful</strong> — Harassment, insults, personal attacks, threats, hate speech, or doxxing
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="guideline_violations[]" value="keep_discussions_constructive" id="violation-constructive">
                            <label class="form-check-label" for="violation-constructive">
                                <strong>Keep Discussions Constructive</strong> — Low-effort spam, off-topic posting, deliberate derailment, or trolling
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="guideline_violations[]" value="no_spam_or_abuse" id="violation-spam">
                            <label class="form-check-label" for="violation-spam">
                                <strong>No Spam or Abuse</strong> — Advertising, self-promotion, referral links, automated posting, or manipulation
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="guideline_violations[]" value="content_standards" id="violation-content">
                            <label class="form-check-label" for="violation-content">
                                <strong>Content Standards</strong> — Illegal content, sexually explicit material, violence, scams, or malicious code
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="guideline_violations[]" value="accounts_and_identity" id="violation-identity">
                            <label class="form-check-label" for="violation-identity">
                                <strong>Accounts and Identity</strong> — Impersonation, misrepresentation, or evading moderation
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="report-additional-notes" class="form-label">Additional notes (optional)</label>
                        <textarea class="form-control" id="report-additional-notes" name="additional_notes" rows="3" 
                                  placeholder="Provide any additional context that may help moderators understand the issue..."></textarea>
                    </div>
                    
                    <div class="alert alert-info mb-0">
                        <small>Reports are reviewed by moderators. False or malicious reports may result in action against your account.</small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
