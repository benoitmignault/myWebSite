import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { FaHouse } from "react-icons/fa6";
import { MdLogout } from "react-icons/md";
import { FaArrowUp } from "react-icons/fa";
import { BsCameraFill } from "react-icons/bs";
import { MdAdminPanelSettings } from "react-icons/md";
import { LuTrophy, LuHouse, LuChartLine } from "react-icons/lu";
import PeriodSelector from "../PeriodSelector";



import { API_BASE_URL } from "../../../../config";
import Footer from "../../../Footer";

import '../../../../css/admin.css';
import "../../../../css/analytics.css";

/**
 * Composant pour afficher les statistiques d'activité du site web et des partenaires,
 * avec la possibilité de sélectionner une période pour filtrer les données.
 * 
 * @returns {JSX.Element} - Le composant Analytics.
 */
function SponsorAnalytics() {

};

export default SponsorAnalytics;