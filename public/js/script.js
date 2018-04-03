$("#deleteButton").click(function() {
    if (!confirm("Attention, si vous continuez, votre compte sera définitivement supprimé.")){
        return false;
    }
});