/**
 * Indique si ce navigateur doit être exclu des statistiques.
 */
export function shouldExcludeStats() {
    return localStorage.getItem("exclude_stats") === "true";
}

/**
 * Exclure ce navigateur des statistiques.
 */
export function excludeStats() {
    localStorage.setItem("exclude_stats", "true");
}

/**
 * Réinclure ce navigateur dans les statistiques.
 */
export function includeStats() {
    localStorage.removeItem("exclude_stats");
}