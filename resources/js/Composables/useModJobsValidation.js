import { watch } from "vue";

/**
 * Evidenzia i campi FormKit "required" non compilati nelle sezioni dinamiche
 * (customers_mod_jobs_schema/_values) e marca con un asterisco il titolo
 * della sezione/tab che li contiene, tenendo il tutto sincronizzato mentre
 * l'utente compila il form.
 */
function markRequiredFields(nodeArray, values) {
    if (!Array.isArray(nodeArray)) {
        return false;
    }

    let sectionHasError = false;

    nodeArray.forEach(node => {
        if (node.validation && node.validation.includes('required')) {
            const value = values[node.name];
            const currentInputClass = node.classes?.input || '';
            const errorClass = '!border !border-red-500';

            if (
                value === undefined ||
                value === null ||
                (typeof value === 'string' && value.trim() === '') ||
                (Array.isArray(value) && value.length === 0)
            ) {
                sectionHasError = true;

                node.classes = node.classes || {};
                node.classes.input = `${currentInputClass} ${errorClass}`.trim();
            } else {
                node.classes = node.classes || {};
                node.classes.input = currentInputClass.replace(errorClass, '').trim();
            }
        }

        if (Array.isArray(node.children)) {
            if (markRequiredFields(node.children, values)) {
                sectionHasError = true;
            }
        }
    });

    return sectionHasError;
}

async function validateHasError(form) {
    let hasErrorGlobal = false;

    form.customers_mod_jobs_schema.forEach(section => {
        let schema = JSON.parse(section.schema);

        const hasError = markRequiredFields(schema, form.customers_mod_jobs_values);

        if (hasError) {
            if (!section.title.includes('*')) {
                section.title = `${section.title} *`;
            }
            section.error = hasError;
            hasErrorGlobal = true;
        } else {
            section.title = section.title.replace(/\s\*$/, '');
            section.error = '';
        }

        section.schema = JSON.stringify(schema);
    });

    return hasErrorGlobal;
}

export function useModJobsValidation(form) {
    watch(
        () => form.customers_mod_jobs_values,
        () => {
            validateHasError(form);
        },
        { deep: true }
    );
}
