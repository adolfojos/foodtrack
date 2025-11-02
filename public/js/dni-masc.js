 const options = {
            translation: {
                '0': {
                    pattern: /\d/
                },
                '1': {
                    pattern: /[1-9]/
                },
                '9': {
                    pattern: /\d/,
                    optional: true
                },
                '#': {
                    pattern: /\d/,
                    recursive: true
                },
                'C': {
                    pattern: /[VvEe]/,
                    fallback: 'V'
                }
            }
        };
        $('#document_id').mask('C-19999999', options);